<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'qtype_similarity_calculator', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage shortanssimilarity
 * @copyright  2021 Yash Srivastava - VIP Research Group (ysrivast@ualberta.ca)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Short Answer Similarity';
$string['pluginname_help'] = 'Create a short answer similarity question type with insert question description here.';
$string['pluginname_link'] = 'question/type/shortanssimilarity';
$string['pluginnameadding'] = 'Adding a short answer similarity question';
$string['pluginnameediting'] = 'Editing a short answer similarity question';
$string['pluginnamesummary'] = 'A short answer similarity question type that allows a students written response answer to be automatically marked.

In collaboraion with VIP Reasearch Group.';

$string['empty_string'] = '';

$string['key_text'] = 'what is key text?';
$string['key_text_required'] = 'Key Text is required.';
$string['key_text_help'] = 'This is the answer to the question. The Key Text is the text our system will use to compare to the students response.';

$string['manual_marking'] = 'maunal marking vs automatic marking';
$string['manual_marking_help'] = '- Maunal Marking: Requires cron. If set to yes, this question will default to maunal marking and the question will be marked in the background. Once the question is finished marking, the question text will be updated with the result (This will be visible to both teachers and students but will not actually mark the question). From there the teacher can review the question and update the mark. Use IF the provided answer (Key Text) is longer than 2 sentences.

- Automatic Marking: Does not require cron. If this option is set to no, then will use automatic marking. Although, load times will be drastically longer (can last 10 minutes per question, if responses are compilcated). Therefore we recommend this option be set to no, IF the answer (Key Text) is less then or equal to 2 sentences.';

$string['summarize_repsponse_valid'] = 'The user entered: ';
$string['summarize_repsponse_invalid'] = 'The user failed to enter a response.';

$string['validation_error_no_response'] = 'No repsonse submitted.';
$string['validation_error_error'] = 'there was an unexpected error';

$string['is_completed_true'] = 'The calculated mark for this response was: {$a}';
$string['is_completed_false'] = 'This question has not been marked yet by our systems - please check back later.';
