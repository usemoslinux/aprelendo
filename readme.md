# Aprelendo

Aprelendo is a 100% free and 100% open-source language-learning platform built to help you turn real content into active vocabulary.

It is especially useful for intermediate and advanced learners who already know the basics but feel stuck on a plateau. Instead of asking you to learn isolated words first, Aprelendo starts with material you actually want to read or watch, then helps you revisit that vocabulary until you can understand it, recall it, and use it more confidently.

Aprelendo is under active development. There is no paid tier, no ads, and no plan to introduce hidden fees later.

## What Aprelendo is for

Aprelendo helps you study from real material, especially:

- ebooks
- audiobooks and audio-backed texts
- YouTube videos
- offline videos
- web texts and articles

The goal is simple: capture words in context while you read or watch, then keep practicing them in ways that fit your time and level.

## How it works

A typical Aprelendo workflow looks like this:

1. Import a text or video you actually care about.
2. Read or watch it inside the learning interface.
3. Look up unfamiliar words and save them directly from context.
4. Revisit the same vocabulary through review, flashcards, cloze cards, or assisted phases such as listening, speaking, and dictation.

This makes vocabulary collection less dull than building cards by hand from scratch. Instead of inventing examples later, you add words from real sentences you already encountered.

## Total reading is optional

Aprelendo includes an assisted workflow called total reading. It is designed to help one piece of content become several kinds of practice: reading, listening, speaking, dictation, and review.

Total reading is not presented here as the one true method for language learning, and it is not a replacement for grammar study, conversation, classes, or other tools. It is a complementary workflow inside Aprelendo for learners who want deeper practice from the same material.

If you prefer a lighter setup, you can rely on reading plus flashcards or cloze review instead. On busy days, that may be the better option.

## Why Aprelendo is different

- It is built around real content rather than generic beginner exercises.
- It helps you keep vocabulary tied to the sentence, audio, or scene where you found it.
- It gives intermediate and advanced learners a practical way to keep progressing after the beginner stage.
- It combines immersive input with lighter review tools, instead of forcing you to choose one approach.
- It is fully open source and intended to stay free for everyone.

## Installation

1. Install Docker

Follow the appropriate installation instructions described [here](https://docs.docker.com/engine/install/).

2. Clone repository

```bash
git clone https://github.com/usemoslinux/aprelendo.git
cd aprelendo
```

3. Run first-time install script

```bash
./install.sh
```

This script provides installation progress feedback and does the following:

- Creates `src/config/config.php` from template if missing.
- Creates `docker-compose.yml` from template if missing.
- Builds and starts containers.
- Runs one-time DB bootstrap using `src/scripts/install.php`.

4. Update local credentials

In `src/config/config.php`:

- Change `DB_USER` and `DB_PASSWORD`.
- Change API credentials (YouTube, Google Drive, etc.).
- Change email credentials.

Optional commands:

- Rebuild schema from scratch: `./install.sh --force`
- Run DB bootstrap only: `docker compose exec -T php php src/scripts/install.php`
- Run DB bootstrap via Composer: `composer run install:db`

## Contributing

If you want to help improve Aprelendo, there are several useful ways to contribute:

- Report bugs or suggest improvements through [GitHub Issues](https://github.com/usemoslinux/aprelendo/issues).
- Share Aprelendo with learners, teachers, friends, or colleagues who may benefit from it.
- Contribute code, fixes, translations, or documentation through the [GitHub repository](https://github.com/usemoslinux/aprelendo).

## Support

Aprelendo is free and open source, with no ads or locked features. If you use it and want to support hosting and development, you can make a donation:

- [PayPal](https://www.paypal.com/ncp/payment/GJCS2645TD9GN)

## License

This project is licensed under the GPL v3 License. See [license.md](license.md) for details.
