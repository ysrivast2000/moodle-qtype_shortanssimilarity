# Short Answer Similarity Calculator

This plugin is a moodle question type that can be added to any moodle quiz.

The plugin extracts the text from the answer provided by teacher and from the student's response. Once the two strings are extracted, the similarity between the two multi-sentences is calculated by the VIP Research Group's multi sentence similarity calculator web service.

## Installation

### Installation Using Git 

To install using git for the latest version (the master branch), type this command in the
root of your Moodle install:

    git clone https://github.com/ysrivast2000/moodle-qtype_shortanssimilarity.git question/type/shortanssimilarity

### Installation From Downloaded zip file

Alternatively, download the zip (can be found at): https://github.com/ysriavst2000/moodle-qtype_shortanssimilarity

unzip it into the question/type folder, and then rename the new folder to shortanssimilairty.

## Who should use

This is one alternative start for devloping a question type plug in and is working code as is. Although it doesn't do any actual
grading or collect student input at all.

Depending on what type of question plug in you want to develope it might be good to either :

* use one of the existing question types that is doing something similar to what you want to do as a base, copy that,
have fun deleting no longer needed code and you then have a template to start from.
* or if possible to avoid code duplication it is better to extend existing classes, particularly for the question type and
question classes. There are quite a few examples of queston types that do this at https://github.com/moodleou/.
        for example classes in ddimageortext and ddmarker both inherit from common code in ddimageortext and those inherit code from the gapselect question type
* or this code might help start you off.
