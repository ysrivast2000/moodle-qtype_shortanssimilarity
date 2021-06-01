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
 * short answer similarity question definition class.
 *
 * @package    qtype
 * @subpackage shortanssimilarity
 * @copyright  2021 Yash Srivatava - VIP Research Group (ysrivast@ualberta.ca
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();



// require_once($CFG->dirroot . '/question/type/shortanssimilarity/classes/task/calculator.php');
require_once($CFG->dirroot . '/question/type/questionbase.php');

/**
 * Question type class for the short answer similarity question.
 *
 * @copyright  2021 Yash Srivatava - VIP Research Group (ysrivast@ualberta.ca)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_shortanssimilarity_question extends question_with_responses implements question_automatically_gradable {

    /** @var int
     * Indicates whether we should mark with cron
     */
    public $usecron;

    /**
     * Set the behaviour of the question as manual graded
     */
    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        global $DB;

        $question = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $this->id));

        if ($question->manual_grading == 1) {
            return question_engine::make_behaviour('manualgraded', $qa, $preferredbehaviour);
        } else {
            return question_engine::make_archetypal_behaviour($preferredbehaviour, $qa);
        }
    }


    /**
     * Data to be included in the form submission when a student submits the question
     * in it's current state
     *
     * @return array
     */
    public function get_expected_data() {
        return array('answer' => PARAM_RAW);
    }


    /**
     * @param moodle_page the page we are outputting to.
     * @return qtype_essay_format_renderer_base the response-format-specific renderer.
     */
    public function get_format_renderer(moodle_page $page) {
        return $page->get_renderer('qtype_essay', 'FORMAT_PLAIN');
    }

    /**
     * Start a new attempt at this question, storing any information that will
     * be needed later in the step.
     *
     * This is where the question can do any initialisation required on a
     * per-attempt basis. For example, this is where the multiple choice
     * question type randomly shuffles the choices (if that option is set).
     *
     * Any information about how the question has been set up for this attempt
     * should be stored in the $step, by calling $step->set_qt_var(...).
     *
     * @param question_attempt_step The first step of the {@link question_attempt}
     *      being started. Can be used to store state.
     * @param int $varant which variant of this question to start. Will be between
     *      1 and {@link get_num_variants()} inclusive.
     */
    public function start_attempt(question_attempt_step $step, $variant) {
        global $DB;

        $question = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $this->id));
        $question->finished = 0;
        $question->result = 0;
        $DB->update_record('qtype_shortanssimilarity', $question);
    }


    /**
     * Produce a plain text summar of a response
     *
     * @return summary a string that summarises how the user responded. This
     * is used in the quiz responses report
     * */
    public function summarise_response(array $response) {
        $output = null;

        if (isset($response['answer'])) {
            $output .= get_string('summarize_repsponse_valid', 'qtype_shortanssimilarity') . $response['answer'];
        } else {
            $output .= get_string('summarize_repsponse_invalid', 'qtype_shortanssimilarity');
        }

        return $output;
    }

    public function un_summarise_response(string $summary) {
        if (!empty($summary)) {
            return array('answer' => text_to_html($summary));
        } else {
            return array();
        }
    }

    public function get_matching_answer(array $response) {
        global $DB;

        $question = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $this->id));
        $fraction = $question->result;

        return array('fraction' => $fraction);
    }

    public function using_chron() {
        global $DB;

        $question = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $this->id));

        if ($question->manual_grading == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function get_grade() {
        global $DB;

        $question = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $this->id));
        $score = $question->result * $this->defaultmark;
        return $score;
    }

    public function is_complete_response(array $response) {
        global $DB;

        $question = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $this->id));

        if (array_key_exists('answer', $response) && ($response['answer'] !== '')) {
            if ($question->manual_grading == 1) {
                $question = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $this->id));
                $fraction = $this->calculate_simularity($question, $response);
            }

            return true;
        } else {
            return false;
        }
    }

    public function get_validation_error(array $response) {
        if (!$this->is_complete_response($response)) {
            return get_string('validation_error_no_response', 'qtype_shortanssimilarity');
        } else {
            return get_string('empty_string', 'qtype_shortanssimilarity');
        }

        return get_string('validation_error_error', 'qtype_shortanssimilarity');
    }

    public function is_completed_marking() {
        global $DB;

        $question = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $this->id));

        if ($question->finished == 0) {
            return false;
        } else {
            $score = $question->result * $this->defaultmark;
            return true;
        }
    }

    /**
     * if you are moving from viewing one question to another this will
     * discard the processing if the answer has not changed. If you don't
     * use this method it will constantantly generate new question steps and
     * the question will be repeatedly set to incomplete. This is a comparison of
     * the equality of two arrays.
     * Comment from base class:
     *
     * Use by many of the behaviours to determine whether the student's
     * response has changed. This is normally used to determine that a new set
     * of responses can safely be discarded.
     *
     * @param array $prevresponse the responses previously recorded for this question,
     *      as returned by {@link question_attempt_step::get_qt_data()}
     * @param array $newresponse the new responses, in the same format.
     * @return bool whether the two sets of responses are the same - that is
     *      whether the new set of responses can safely be discarded.
     */

    public function is_same_response(array $prevresponse, array $newresponse) {
        if (array_key_exists('answer', $prevresponse) && $prevresponse['answer'] !== '') {
            $value1 = (string) $prevresponse['answer'];
        } else {
            $value1 = '';
        }

        if (array_key_exists('answer', $newresponse) && $newresponse['answer'] !== '') {
            $value2 = (string) $newresponse['answer'];
        } else {
            $value2 = '';
        }

        return ($value1 === $value2 || question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer'));
    }

     /**
      * @return question_answer an answer that
      * contains the a response that would get full marks.
      * used in preview mode. If this doesn't return a
      * correct value the button labeled "Fill in correct response"
      * in the preview form will not work. This value gets written
      * into the rightanswer field of the question_attempts table
      * when a quiz containing this question starts.
      */
    public function get_correct_response() {
        return null;
    }


    public function calculate_simularity($question, $response) {
        global $DB;

          $task =  new qtype_shortanssimilarity\calculator();
        $task->set_custom_data(array(
            'key' => $question->key_text,
            'target' => $response['answer'],
            'value' => 1,
            'maxBPM' => true,
            'language' => 'en',
            'id' => $question->id
        ));

        \core\task\manager::queue_adhoc_task($task);

        $options = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $question->questionid));
        return $options->result;
    }


    /**
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return array (number, integer) the fraction, and the state.
     */
    public function grade_response(array $response) {
        global $DB;

        $question = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $this->id));

        // Prepare object to be sent to VIP Research's multi-sentence
        // short answer similarity.
        $json = array(
            'key' => $question->key_text,
            'target' => $response['answer'],
            'value' => 1,
            'maxBPM' => 1,
            'language' => $question->item_language
        );

        $json = json_encode($json);

        $context = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => $json
            )
        );
        $context  = stream_context_create($context);

        // Use file_get_get_contents and json_decode to capture response.

        /**
         * NOTE: Bridge v1 does not work here as the required parameters are not satisfied.
         *
         *
         */

        $contents = file_get_contents('https://ws-nlp.vipresearch.ca/bridge/v2/', false, $context);
        $contents = json_decode($contents);

        // Update database with new values.
        $options = $DB->get_record('qtype_shortanssimilarity', array('id' => $question->id));
        $options->result = $contents->similarity;
        $options->finished = 1;
        $DB->update_record('qtype_shortanssimilarity', $options);

        return array((double) $contents->similarity,
            question_state::graded_state_for_fraction((double) $contents->similarity));
    }


    /**
     * Get one of the question hints. The question_attempt is passed in case
     * the question type wants to do something complex. For example, the
     * multiple choice with multiple responses question type will turn off most
     * of the hint options if the student has selected too many opitions.
     * @param int $hintnumber Which hint to display. Indexed starting from 0
     * @param question_attempt $qa The question_attempt.
     */
    public function get_hint($hintnumber, question_attempt $qa) {
        return null;
    }

    /**
     * Generate a brief, plain-text, summary of the correct answer to this question.
     * This is used by various reports, and can also be useful when testing.
     * This method will return null if such a summary is not possible, or
     * inappropriate.
     * @return string|null a plain text summary of the right answer to this question.
     */
    public function get_right_answer_summary() {
        return '';
    }

}
