#!/usr/bin/env python3
"""
Fetch a YouTube transcript as JSON using youtube_transcript_api.

Usage:
    fetch-transcript.py VIDEO_ID lang1,lang2

- Only manually created subtitles are returned (auto-generated are skipped).
- Outputs a JSON list with a single transcript (to match existing PHP expectations).
"""

import sys
import json
from youtube_transcript_api import YouTubeTranscriptApi
from youtube_transcript_api._errors import (
    TranscriptsDisabled,
    NoTranscriptFound,
    VideoUnavailable
)


def fetch_transcript(video_id, languages):
    """
    Fetch manually created transcript for the given video ID.
    
    Args:
        video_id: YouTube video ID
        languages: List of language codes to try
    
    Returns:
        List containing a single transcript dict, or empty list if none found
    """
    try:
        # Initialize API and get list of available transcripts
        ytt_api = YouTubeTranscriptApi()
        transcript_list = ytt_api.list(video_id)
        
        # Try each language in order
        for lang in languages:
            # Iterate through available transcripts to find manually created one
            for transcript in transcript_list:
                # Check if this transcript matches the language and is manually created
                if transcript.language_code == lang and not transcript.is_generated:
                    # Fetch the transcript data
                    fetched_transcript = transcript.fetch()
                    
                    # Convert snippets to dictionaries
                    transcript_data = []
                    for snippet in fetched_transcript.snippets:
                        transcript_data.append({
                            'text': snippet.text,
                            'start': snippet.start,
                            'duration': snippet.duration
                        })
                    
                    # Return just the transcript data
                    return transcript_data
        
        # No manually created transcript found in any requested language
        return []
        
    except TranscriptsDisabled:
        print("Error: Transcripts are disabled for this video", file=sys.stderr)
        return []
    except VideoUnavailable:
        print("Error: Video is unavailable", file=sys.stderr)
        return []
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)
        return []


def main():
    if len(sys.argv) != 3:
        print(__doc__)
        sys.exit(1)
    
    video_id = sys.argv[1]
    languages = sys.argv[2].split(',')
    
    # Fetch transcript
    result = fetch_transcript(video_id, languages)
    
    # Output as JSON
    print(json.dumps(result, indent=2, ensure_ascii=False))


if __name__ == '__main__':
    main()