# Short Answer Similarity Question Type

This plugin is a moodle question type that can be added to any moodle quiz.

The plugin extracts the text from the answer provided by teacher and from the student's response. Once the two strings are extracted, the similarity between the two multi-sentences is calculated by the VIP Research Group's multi sentence similarity calculator web service.

## Installation

### Installation Using Git 

To install using git for the latest version (the master branch), type this command in the
root of your Moodle install:

    git clone https://github.com/ysrivast2000/moodle-qtype_shortanssimilarity.git question/type/shortanssimilarity

### Installation From Downloaded zip file

Alternatively, download the zip (can be found at): https://github.com/ysrivast2000/moodle-qtype_shortanssimilarity

unzip it into the question/type folder, and then rename the new folder to shortanssimilairty.

## How To Use

If properly installed, when adding questions to a quiz in Moodle, the option to add a "Short Answer Similarity" question should be available. Fill in the required form data and save the question

### Manual Marking on or off?

This section explains the option to turn manual marking on or off in the question creation page.

- Maunal Marking: Requires cron. If the manual marking option is set to yes, this question will default to maunal marking and the question will be marked in the background. Once the question is finished marking, the question text will be updated with the result (This will be visible to both teachers and students but will not actually mark the question). From there the teacher can review the question and update the mark. Use IF the provided answer (Key Text) is longer than 2 sentences.

- Automatic Marking: Does not require cron. If the manual marking option to no, then will use automatic marking. Although, load times will be drastically longer (can last 10 minutes per question, if responses are compilcated). Therefore we recommend this option be set to no, IF the answer (Key Text) is less then or equal to 2 sentences.';

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see http://www.gnu.org/licenses/.
