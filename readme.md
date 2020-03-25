# Aprelendo

Let's start by saying that Aprelendo is still under heavy development.

I started developing it to help me learn new languages. It is based on the idea that acquiring vocabulary by reading texts that are of interest to us is easier, more appealing and -more importantly- more effective than reviewing stand alone flashcards, as most language learning software seem to do these days.

## Why are flashcards bad for language learning?

Programs like [Learning with Texts (LWT)](http://lwt.sourceforge.net/) and [Readlang](https://readlang.com/) are similar to Aprelendo, but they end up skewing the reading process by converting it into a merely extractive process in which people end up focusing on creating flashcards. Reading becomes an instrument in that boring and tiresome scheme.

If you ever used [Anki](https://apps.ankiweb.net/) and other [spaced repetition software](https://en.wikipedia.org/wiki/Spaced_repetition) you probably know that creating new flashcards can become a very tiresome and time consuming task and, after a while, reviewing them also becomes dull and monotonous. 

## Let's try an alternative: total reading

Reading has some nice benefits. It allows us to acquire vocabulary in context, by presenting words and phrases as they are used, including grammar, spelling, inflections, etc. Also, it is easier to realize the importance of commonly used words or phrases when we read them often.

These benefits are enhaced if we are interested in the text's subject. It is evident that , the context becomes even more relevant and memorable, therefore facilitating vocabulary acquisition. This will help us learn vocabulary that we know we will often want to use ourselves, therefore creating a hidden need to incorporate it.

Reading has one final advantage: we already do it a lot as we surf the Web. We only need to take advantage of this to learn new languages. However, the biggest limitation of doing this is that it would only cover one of the four dimensions mentioned above. To become "total", the reading process has to meet certain criteria (which usually does not):

  - Texts should be short (the length of a newspaper article or a short story, not a book)

  - You should be compelled to read the same text a couple of times so that you really understand it fully (memorizing something without understanding it in the first place is useless).

  - You should start by focusing on understanding the general meaning of the text first, then its parts (paragraphs, phrases and specific words)

  - You should be able to search the meaning of new words and phrases in a very transparent way. Adding them to your to your learning stack should also be easy.

  - Words and phrases that are being learned should be highlighted somehow, as a way to let you check if you understand their meaning in each particular context

  - Texts should include audio

  - You should be encouraged to read the text out loud, trying to imitate the audio that is being played. Training mouth muscles is key to achieving a good accent. A natural and stress-free way of improving your speaking skills is by repeating someone else's words without thinking what is the correct way to say this or that, or how the phrase should be constructed. After a while, this will become second nature to you.

## How does Aprelendo implement total reading?

Aprelendo allows reading texts in two ways: free and assisted learning modes. The first one lets you read texts without constraints, while the second one requires you to go through the text at least four times: the first, to understand its general meaning, the second to listen to the audio transcript, the third to read the text out loua in order to practice pronunciation, and last one (which consists of a dictation) to practice how to write the words you marked for learning.

By using Aprelendo you will be practicing all four dimensions of the language you want to learn at the same time, in a systematic and integrated way.

# Prerequisites

  * Nginx/Apache
  * PHP
  * MySQL
  * Ajax
  * HTML/CSS

# Installation

1. Install and configure an Apache/Nginx server with MySQL support

2. Install and configure MySQL

3. Import the file config/aprelendo-template.sql to MySQL and change the database name to "aprelendo"

4. Copy the contents of this git to your website's root folder

# License

This project is licensed under the GPL v3 License - see the [license.md](license.md) file for details.
