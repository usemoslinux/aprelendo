#!/usr/bin/env python3
"""
Clean EPUB for text-only rendering while preserving the cover page.

- Removes: CSS, fonts, non-cover images, scripts, class attributes, formatting tags
- Preserves: cover page structure, OPF metadata, cover image, XML prolog, namespaces
- Outputs XHTML/XML when input is XHTML/XML
- Skips ALL stripping for the navigation file(s) (EPUB 2 NCX and EPUB 3 nav XHTML)

Usage:
    python epub_cleaner.py input.epub output.epub
"""

import argparse
import posixpath
import zipfile
from xml.etree import ElementTree as ET

NS = {
    "opf": "http://www.idpf.org/2007/opf",
    "container": "urn:oasis:names:tc:opendocument:xmlns:container",
    "xhtml": "http://www.w3.org/1999/xhtml",
    "svg": "http://www.w3.org/2000/svg",
    "xlink": "http://www.w3.org/1999/xlink",
}

for _prefix, _uri in NS.items():
    if _prefix != "":
        ET.register_namespace(_prefix, _uri)

FONT_MEDIA_TYPES = {
    "font/otf", "font/ttf", "font/woff", "font/woff2",
    "application/vnd.ms-opentype", "application/font-sfnt", "application/font-woff"
}
CSS_MEDIA_TYPES = {"text/css"}
IMAGE_MEDIA_PREFIX = "image/"

FONT_EXTS = (".otf", ".ttf", ".woff", ".woff2", ".eot")
CSS_EXTS = (".css",)
IMAGE_EXTS = (".jpg", ".jpeg", ".png", ".gif", ".svg", ".bmp", ".webp", ".tif", ".tiff")

# Read XML from bytes
def read_xml(data):
    try:
        return ET.fromstring(data)
    except ET.ParseError:
        return None

# Find OPF file path from container.xml
def find_opf_path(zf):
    container_data = zf.read("META-INF/container.xml")
    root = read_xml(container_data)
    if root is None:
        raise RuntimeError("Invalid container.xml")
    rootfile = root.find(".//container:rootfile", NS)
    if rootfile is None:
        raise RuntimeError("No rootfile found")
    return rootfile.get("full-path")

# Resolve relative paths
def resolve_path(base_dir, href):
    if not href or href.startswith("data:"):
        return href
    return posixpath.normpath(posixpath.join(base_dir, href))

# Detect cover image path from OPF metadata
def detect_cover_image_path(opf_root, opf_dir):
    manifest = opf_root.find(".//opf:manifest", NS)
    if manifest is None:
        return None

    # EPUB 3 cover via properties
    for item in manifest.findall("opf:item", NS):
        properties = (item.get("properties") or "").lower()
        if "cover-image" in properties:
            href = item.get("href", "")
            return resolve_path(opf_dir, href)

    # EPUB 2 cover via <meta name="cover" content="...">
    cover_meta = opf_root.find('.//opf:metadata/opf:meta[@name="cover"]', NS)
    if cover_meta is not None:
        cover_id = cover_meta.get("content")
        if cover_id:
            for item in manifest.findall("opf:item", NS):
                if item.get("id") == cover_id:
                    href = item.get("href", "")
                    return resolve_path(opf_dir, href)
    return None

# Detect all navigation resources (EPUB 3 nav XHTML and EPUB 2 NCX)
def detect_nav_paths(opf_root, opf_dir):
    paths = set()
    manifest = opf_root.find(".//opf:manifest", NS)
    if manifest is None:
        return paths

    # EPUB 3: properties="nav"
    for item in manifest.findall("opf:item", NS):
        props = (item.get("properties") or "").lower()
        href = item.get("href", "")
        if "nav" in props and href:
            paths.add(resolve_path(opf_dir, href))

    # EPUB 2: spine toc="idref" -> NCX
    spine = opf_root.find(".//opf:spine", NS)
    if spine is not None:
        toc_idref = spine.get("toc")
        if toc_idref:
            for item in manifest.findall("opf:item", NS):
                if item.get("id") == toc_idref:
                    href = item.get("href", "")
                    if href:
                        paths.add(resolve_path(opf_dir, href))

    # EPUB 2 (also catch by media-type)
    for item in manifest.findall("opf:item", NS):
        mt = (item.get("media-type") or "").lower()
        if mt == "application/x-dtbncx":
            href = item.get("href", "")
            if href:
                paths.add(resolve_path(opf_dir, href))

    return paths

# Clean content XHTML pages
def clean_content_page(xhtml_bytes, cover_image_path):
    root = read_xml(xhtml_bytes)
    if root is None:
        return xhtml_bytes

    stripped = xhtml_bytes.lstrip()
    had_xml_decl = stripped.startswith(b"<?xml")

    if isinstance(root.tag, str) and root.tag.startswith("{"):
        default_uri = root.tag.split("}", 1)[0][1:]
        if default_uri:
            try:
                ET.register_namespace("", default_uri)
            except ValueError:
                pass

    for elem in list(root.iter()):
        tag = elem.tag.split("}")[-1] if "}" in elem.tag else elem.tag
        if tag == "link":
            rel = (elem.get("rel") or "").lower()
            if rel in ("stylesheet", "font"):
                parent = find_parent(root, elem)
                if parent is not None:
                    parent.remove(elem)
        elif tag in ("style", "script"):
            parent = find_parent(root, elem)
            if parent is not None:
                parent.remove(elem)

    for elem in root.iter():
        if "style" in elem.attrib:
            del elem.attrib["style"]
        if "class" in elem.attrib:
            del elem.attrib["class"]

    formatting_tags = {"a", "b", "i", "u", "strong", "em"}
    for elem in list(root.iter()):
        tag = elem.tag.split("}")[-1] if "}" in elem.tag else elem.tag
        if tag in formatting_tags:
            parent = find_parent(root, elem)
            if parent is not None:
                idx = list(parent).index(elem)
                if elem.text:
                    if idx == 0:
                        parent.text = (parent.text or "") + elem.text
                    else:
                        prev = parent[idx - 1]
                        prev.tail = (prev.tail or "") + elem.text
                for child in list(elem):
                    parent.insert(idx, child)
                    idx += 1
                if elem.tail:
                    if idx > 0:
                        parent[idx - 1].tail = (parent[idx - 1].tail or "") + elem.tail
                    else:
                        parent.text = (parent.text or "") + elem.tail
                parent.remove(elem)

    for elem in list(root.iter()):
        tag = elem.tag.split("}")[-1] if "}" in elem.tag else elem.tag
        if tag == "img":
            parent = find_parent(root, elem)
            if parent is not None:
                parent.remove(elem)

    for elem in list(root.iter()):
        tag = elem.tag.split("}")[-1] if "}" in elem.tag else elem.tag
        if tag == "svg":
            parent = find_parent(root, elem)
            if parent is not None:
                parent.remove(elem)

    return ET.tostring(
        root,
        encoding="utf-8",
        xml_declaration=had_xml_decl,
        method="xml",
    )

# Find parent of an element
def find_parent(root, child):
    for parent in root.iter():
        if child in list(parent):
            return parent
    return None

# Determine if a file should be removed based on media type and href
def should_remove_file(media_type, href):
    if media_type in FONT_MEDIA_TYPES or media_type in CSS_MEDIA_TYPES:
        return True
    if href:
        lower_href = href.lower()
        if lower_href.endswith(FONT_EXTS) or lower_href.endswith(CSS_EXTS):
            return True
        if lower_href.endswith(IMAGE_EXTS) and "cover" not in lower_href:
            return True
    return False

# Main processing function
def process_epub(input_path, output_path):
    with zipfile.ZipFile(input_path, "r") as zin:
        if "mimetype" not in zin.namelist():
            raise RuntimeError("Invalid EPUB: missing mimetype")

        opf_path = find_opf_path(zin)
        opf_dir = posixpath.dirname(opf_path) if "/" in opf_path else ""
        opf_data = zin.read(opf_path)
        opf_root = read_xml(opf_data)
        if opf_root is None:
            raise RuntimeError("Invalid OPF file")

        cover_image_path = detect_cover_image_path(opf_root, opf_dir)

        manifest = opf_root.find(".//opf:manifest", NS)
        if manifest is None:
            raise RuntimeError("No manifest found")

        # NEW: compute navigation paths (to keep untouched)
        nav_paths = detect_nav_paths(opf_root, opf_dir)

        remove_ids = set()
        items = {}

        for item in manifest.findall("opf:item", NS):
            item_id = item.get("id", "")
            href = item.get("href", "")
            media_type = item.get("media-type", "")
            full_path = resolve_path(opf_dir, href) if href else href
            items[item_id] = (full_path, media_type)
            if should_remove_file(media_type, href):
                if cover_image_path and full_path == cover_image_path:
                    continue
                remove_ids.add(item_id)

        for item in list(manifest.findall("opf:item", NS)):
            if item.get("id") in remove_ids:
                manifest.remove(item)

        with zipfile.ZipFile(output_path, "w", compression=zipfile.ZIP_DEFLATED) as zout:
            zout.writestr("mimetype", b"application/epub+zip", compress_type=zipfile.ZIP_STORED)

            removed_paths = {items[rid][0] for rid in remove_ids}

            for file_path in zin.namelist():
                if file_path == "mimetype":
                    continue
                if file_path in removed_paths:
                    continue

                data = zin.read(file_path)

                if file_path == opf_path:
                    new_opf = ET.tostring(opf_root, encoding="utf-8", xml_declaration=True, method="xml")
                    zout.writestr(file_path, new_opf)
                elif file_path in nav_paths:
                    # NEW: do not modify navigation resources at all
                    zout.writestr(file_path, data)
                elif file_path.lower().endswith((".xhtml", ".html", ".htm")):
                    data = clean_content_page(data, cover_image_path)
                    zout.writestr(file_path, data)
                else:
                    zout.writestr(file_path, data)

    print(f"Cleaned EPUB saved to: {output_path}")

def main():
    parser = argparse.ArgumentParser(description="Clean EPUB: remove CSS, fonts, formatting tags; preserve cover, nav, and namespaces")
    parser.add_argument("input", help="Input EPUB file")
    parser.add_argument("output", help="Output EPUB file")
    args = parser.parse_args()
    process_epub(args.input, args.output)

if __name__ == "__main__":
    main()
