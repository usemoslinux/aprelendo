# Aprelendo

Note: Aprelendo is still under heavy development.

I created this tool with a single goal in mind: to make language learning a more enjoyable and effective experience. Rather than relying on tedious flashcard drills, my approach is centered around the idea that reading texts that captivate you is the key to acquiring vocabulary with ease. This method goes against the grain of traditional language learning software, offering a fresh and engaging way to immerse yourself in a new language.

## Why are flashcards bad for language learning?

Programs like [Learning with Texts (LWT)](http://lwt.sourceforge.net/) and [Readlang](https://readlang.com/) are similar to Aprelendo, but they end up distorting the reading process by converting it into a purely extractive routine in which people focus too much on creating flashcards. Thus, reading becomes an instrument in this boring and tedious scheme.

If you ever used [Anki](https://apps.ankiweb.net/) and other [spaced repetition software](https://en.wikipedia.org/wiki/Spaced_repetition) you probably know that creating new flashcards can become a very tiresome and time consuming task and, after a while, reviewing them also becomes dull and monotonous. 

## Let's try an alternative: total reading

Reading has many benefits. It allows us to acquire vocabulary in context, presenting words and phrases as they are used, including grammar, spelling, inflections, etc. In addition, it is easier to realize the importance of commonly used words or phrases when we read them often.

These advantages are enhanced when we are interested in what the author is saying. In this case, the context makes our study session more relevant and memorable, making it easier to acquire vocabulary. The reason for this is that learning words that we know we will use often creates a hidden need to incorporate them into our long-term memory.

Finally, and most importantly, reading is already a common activity in our daily lives, but it covers only one aspect of language acquisition. To become a more complete and effective tool, it must meet certain criteria:

1. Short texts, such as newspaper articles or short stories, rather than lengthy books.

    Reading shorter texts allows you to quickly and easily comprehend the material, while also maintaining your interest and motivation. This is important because if the text is too long, you may lose focus and be unable to retain the information effectively.

2. Multiple readings of the same text to ensure full comprehension.

    Reading a text multiple times will help you to understand the material more deeply and retain it better. This will also allow you to focus on different aspects of the language each time you read it, such as grammar, vocabulary, spelling, or pronunciation.

3. A focus on understanding the overall meaning before delving into specific words and phrases.

    Before focusing on the specific details, it is important to first understand the main idea of the text. This will give you a better context for understanding the language and will make it easier for you to learn and remember new words and phrases.

4. Easy access to definitions and translations of new vocabulary.

    Having immediate access to definitions and translations of new vocabulary is essential for effective language learning. This allows you to understand the meaning of new words in context and to expand your vocabulary more quickly.

5. Tracking comprehension in context.

    Highlighting new words and phrases helps you to track your progress and identify which words and phrases you still need to work on. This will allow you to focus your efforts on areas where you need the most improvement.

6. The inclusion of audio to improve listening skills.

    Including audio with the text is an important aspect of language learning, as it helps to develop your listening skills. This way, you will be able to improve your ability to understand spoken language, which is an essential part of effective communication. Additionally, listening to audio can also help you to learn pronunciation, intonation, and other important elements of speaking.

7. Encouragement to read out loud and practice speaking skills in a stress-free environment.
  
    Reading out loud allows you to practice your speaking skills in a safe and non-judgmental environment. This is an important aspect of language learning because it helps you to become more confident and comfortable speaking the language. By repeating someone else's words, you can focus on speaking without worrying about the correct way to say something. Over time, this will become second nature to you.

## Unlock total language mastery with Aprelendo's innovative total reading approach

Transform your language learning experience with Aprelendo! This innovative tool offers two approaches to reading: free mode, where you can read texts at your own pace, or assisted mode, which guides you through five powerful phases.

Phase 1: Reading - Dive into the text, and if you come across unfamiliar words or phrases, quickly look them up in the built-in dictionary.

Phase 2: Listening - Take your comprehension to the next level by listening to the automatically generated audio version of the text. Pay particular attention to pronunciation.

Phase 3: Speaking - It's time to put your skills to the test! Speak along with the recording, trying to imitate the pronunciation of each word. If necessary, slow down the recording to your own pace.

Phase 4: Dictation - Relive the good old days of school with this classic learning technique. Dictation reinforces your understanding of words - how they are used in context, spelled, etc.

Phase 5: Review - This is the most important step in long-term language acquisition. Take time to review the underlined words, their meaning, pronunciation and spelling. To make them part of your active vocabulary, try to find examples of alternative sentences.

By using Aprelendo, you will practice the four dimensions of your target language in a systematic and integrated way. Say goodbye to traditional, dull language learning methods and hello to a fun and engaging experience with Aprelendo!

## Installation

1. Install docker

Follow the appropriate installation instructions described [here](https://docs.docker.com/engine/install/).

2. Github clone

```bash
git clone https://github.com/usemoslinux/aprelendo.git
```
3. Create new config file:

```bash
cp aprelendo/src/config/config-example.php aprelendo/src/config/config.php
```

4. In ``aprelendo/src/config/config.php`` and ``aprelendo/docker-compose.yml``:

- Change ``MYSQL_ROOT_PASSWORD``, ``MYSQL_USER`` and ``MYSQL_PASSWORD``.

5. In ``aprelendo/src/config/config.php``:

- Change API credentials (YouTube, Google Drive, etc.)

- Change email credentials (used to send email to new users, retrieve forgotten passwords, etc.).

6. In ``aprelendo`` root directory, run:

```bash
docker compose up -d
```

## Contributing

Do you find the app useful and want to contribute to make it even better? Here are some ways you can help:

* **Report issues.** A very simple way of contributing to the project is to report crashes and bugs, as well as suggest possible new features. You can join our [General Matrix Room](https://matrix.to/#/!gjBUJUxIWqLZeLofbU:matrix.org?via=matrix.org) and share your ideas regarding the app.
* **Share the app.** Tell your friends, family, and colleagues about the app, in real life and online. You could, for example, write a post about Aprelendo on your favorite social media networks.
* **Write code.** If you're able to write PHP/JS/SQL code, please consider working on fixing [bugs](https://github.com/usemoslinux/aprelendo/issues) or implementing new features. If you need help, don't hesitate to join us in our [Contributing Matrix Room](https://matrix.to/#/!EUTYnKqqplfKVYzgTM:matrix.org?via=matrix.org).

## Donate

When I started programming this app I decided that it should be completely free and open-source. This also means that there will be no income for me from ads or paid features. So if you enjoy the app and want to support my work you can do so:

* [Paypal](https://www.paypal.com/ncp/payment/GJCS2645TD9GN)
* [Patreon](https://www.patreon.com/aprelendo/)

You can also join our [Matrix Space](https://matrix.to/#/#aprelendo:matrix.org) to get in touch with me.

## License

This project is licensed under the GPL v3 License - see the [license.md](license.md) file for details.
