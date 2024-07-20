# generate_mp3.py
import base64
from gtts import gTTS
from io import BytesIO

def generate_mp3(text):
    tts = gTTS(text, lang=sys.argv[1])
    mp3_fp = BytesIO()
    tts.write_to_fp(mp3_fp)
    mp3_fp.seek(0)
    return base64.b64encode(mp3_fp.read()).decode('utf-8')

if __name__ == "__main__":
    import sys
    text = sys.argv[2]
    mp3_data = generate_mp3(text)
    print(mp3_data)
