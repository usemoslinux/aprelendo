# Aprelendo

Note: Aprelendo is still under heavy development.

Aprelendo was created with one simple goal: to make language learning enjoyable and effective. Instead of relying on repetitive flashcard drills, it focuses on learning through real and meaningful content—the kind that actually keeps you reading.

The philosophy behind Aprelendo is that reading texts that truly interest you is the most natural and powerful way to acquire vocabulary. By engaging with authentic materials, you absorb words and grammar in their proper context, rather than memorizing them in isolation. This approach breaks away from traditional language-learning software and encourages deep, contextual understanding.

## A new approach: total reading

Reading offers countless advantages. It exposes you to vocabulary in context, showing how words and phrases are used naturally—with all their nuances in grammar, spelling, and meaning. Frequent exposure also helps you identify which words are essential and which are rare, guiding your learning intuitively.

When you read something you care about, this process becomes even more effective. Your emotional engagement enhances memory retention: your brain prioritizes words that feel relevant to you and that you expect to use.

However, reading alone doesn’t automatically make you fluent. To transform reading into a complete language-learning tool, Aprelendo builds upon the following principles:

1. Focus on short, digestible texts—like articles or short stories—instead of long novels.
2. Re-read texts to ensure full comprehension.
3. Prioritize understanding the overall meaning before analysing individual words.
4. Access definitions and translations instantly.
5. Track your progress and comprehension over time.
6. Listen to integrated audio to improve pronunciation and listening skills.
7. Read out loud and practise speaking in a stress-free environment.
  
## Installation

1. Install docker

Follow the appropriate installation instructions described [here](https://docs.docker.com/engine/install/).

2. Github clone

```bash
git clone https://github.com/usemoslinux/aprelendo.git
```
3. Create and modify config file

Copy config template and create new config file:

```bash
cd aprelendo
cp src/config/config-example.php src/config/config.php
```

In ``aprelendo/src/config/config.php``: 

- Change ``MYSQL_USER`` and ``MYSQL_PASSWORD``.

- Change API credentials (YouTube, Google Drive, etc.)

- Change email credentials (used to send email to new users, retrieve forgotten passwords, etc.).

4. Create and modify docker YAML file

```bash
mv docker-compose.yml.example docker-compose.yml
```

In ``docker-compose.yml``: 

- Change ``MYSQL_ROOT_PASSWORD``, ``MYSQL_USER`` and ``MYSQL_PASSWORD``.

5. Build and run container

In ``aprelendo`` root directory, run:

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
