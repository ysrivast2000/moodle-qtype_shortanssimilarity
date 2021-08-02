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
 * Question type class for the short answer similarity question type.
 *
 * @package    qtype
 * @subpackage shortanssimilarity
 * @copyright  2021 Yash Srivastava - VIP Research Group (ysrivast@ualberta.ca)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/shortanssimilarity/question.php');


/**
 * The short answer similarity question type.
 *
 * @copyright  2021 Yash Srivastava - VIP Research Group (ysrivast@ualberta.ca)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_shortanssimilarity extends question_type {
    /**
     * Used to move files along with questions.
     * @param int $questionid with respect to each question id. Indexed starting from 0
     * @param int $oldcontextid contains old context id. Indexed starting from 0
     * @param int $newcontextid contains old newcontextid . Indexed starting from 0
     */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_answers($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }
    /**
     * Used to delete files.
     * @param int $questionid with respect to each question id. Indexed starting from 0
     * @param int $contextid contains context id. Indexed starting from 0
     * @return void.
     */
    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }
    /**
     * Used to saves questions.
     * @param $question contains questions.
     * @return void.
     */
    public function save_question_options($question) {
        global $DB;

        $options = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $question->id));

        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->key_text = '';
            $options->item_language = 'en';
            $options->result = 0;
            $options->finished = 0;
            $options->manual_grading = 0;
            $options->id = $DB->insert_record('qtype_shortanssimilarity', $options);
        }

        $options->key_text = $question->key;
        $options->item_language = $question->language;
        $options->manual_grading = $question->manual_grading;

        $DB->update_record('qtype_shortanssimilarity', $options);

        $this->save_hints($question);
    }
    /**
     * Used to populates fields such as combined feedback.
     * also make $DB calls to get data from other tables.
     * @param $question contains questions.
     * @return void.
     */
    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('qtype_shortanssimilarity', array('questionid' => $question->id), '*', MUST_EXIST);
        parent::get_question_options($question);
    }

    /**
     * Executed at runtime (e.g. in a quiz or preview)
     * @param $question contains the questions.
     * @param question_definition $questiondata contains the question data.
     * @return void.
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->key_text = $questiondata->options->key_text;
        $question->item_language = $questiondata->options->item_language;
        $question->result = $questiondata->options->result;
        $question->finished = $questiondata->options->finished;
        $question->manual_grading = $questiondata->options->manual_grading;
    }
    /**
     * Used to delete questions.
     * @param int $questionid with respect to each question id. Indexed starting from 0
     * @param int $contextid contains old context id. Indexed starting from 0.
     * @return void.
     */
    public function delete_questions($questionid, $contextid) {
        global $DB;

        $DB->$DB->delete_records('qtype_shortanssimilarity', array('questionid' => $question->id));
        parent::delete_questions($questionid, $contextid);
    }

    /**
     * Used to import data from xml.
     * @param $data with respect to each question id. Indexed starting from 0.
     * @param $question contains the question.
     * @param $format contains the format type.
     * @param $extra.
     * @return $question or boolval
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'shortanssimilarity') {
            return false;
        }
        $question = parent::import_from_xml($data, $question, $format, null);
        $format->import_combined_feedback($question, $data, true);
        $format->import_hints($question, $data, true, false, $format->get_format($question->questiontextformat));
        return $question;
    }
    /**
     * Used to export data from xml.
     * @param $question contains the question.
     * @param $format contains the format type.
     * @param $extra.
     * @return $ouput.
     */
    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        global $CFG;
        $pluginmanager = core_plugin_manager::instance();
        $gapfillinfo = $pluginmanager->get_plugin_info('shortanssimilarity');
        $output = parent::export_to_xml($question, $format);
        $output .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);
        return $output;
    }
    /**
     * Used to generate a random score.
     * @param $questiondata contains the question data.
     * @return 0 value.
     */
    public function get_random_guess_score($questiondata) {
        return 0;
    }
    /**
     * Used to generate a actual score.
     * @param $questiondata contains the question data.
     * @return array containing question data.
     */
    public function get_possible_responses($questiondata) {
        return array($questiondata->options->id => $questiondata->options->key_text);
    }
}
