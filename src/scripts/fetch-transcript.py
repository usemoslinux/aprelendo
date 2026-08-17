#!/usr/bin/env python3
"""
Fetch a YouTube transcript as JSON using youtube_transcript_api.

Usage:
    fetch-transcript.py VIDEO_ID target_language

- Manually created target-language subtitles are preferred.
- Auto-generated subtitles are used only when no matching manual track exists.
"""

import sys
import json
from youtube_transcript_api import YouTubeTranscriptApi
from youtube_transcript_api._errors import TranscriptsDisabled, VideoUnavailable


def normalize_language_code(language_code):
    return language_code.strip().replace('_', '-').lower()


def fetch_transcript(video_id, target_language):
    """
    Fetch manually created transcript for the given video ID.
    
    Args:
        video_id: YouTube video ID
        target_language: Base language code selected in Aprelendo
    
    Returns:
        Transcript metadata and snippets, or None if no matching track exists
    """
    try:
        # Initialize API and get list of available transcripts
        ytt_api = YouTubeTranscriptApi()
        transcript_list = ytt_api.list(video_id)
        
        target = normalize_language_code(target_language)
        matching_transcripts = []

        for transcript in transcript_list:
            language_code = normalize_language_code(transcript.language_code)
            if language_code == target or language_code.startswith(target + '-'):
                matching_transcripts.append(transcript)

        # Prefer manual tracks across all matching regional variants.
        matching_transcripts.sort(key=lambda transcript: (
            transcript.is_generated,
            normalize_language_code(transcript.language_code) != target,
            normalize_language_code(transcript.language_code)
        ))

        if not matching_transcripts:
            return None

        transcript = matching_transcripts[0]
        fetched_transcript = transcript.fetch()
        transcript_data = []

        for snippet in fetched_transcript.snippets:
            transcript_data.append({
                'text': snippet.text,
                'start': snippet.start,
                'duration': snippet.duration
            })

        return {
            'language_code': transcript.language_code,
            'is_generated': transcript.is_generated,
            'snippets': transcript_data
        }
        
    except TranscriptsDisabled:
        print("Error: Transcripts are disabled for this video", file=sys.stderr)
        return None
    except VideoUnavailable:
        print("Error: Video is unavailable", file=sys.stderr)
        return None
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)
        return None


def main():
    if len(sys.argv) != 3:
        print(__doc__)
        sys.exit(1)
    
    video_id = sys.argv[1]
    target_language = sys.argv[2]
    
    # Fetch transcript
    result = fetch_transcript(video_id, target_language)
    
    # Output as JSON
    print(json.dumps(result, indent=2, ensure_ascii=False))


if __name__ == '__main__':
    main()
