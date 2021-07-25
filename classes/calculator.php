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
 * Privacy Subsystem implementation for qtype_calculatedmulti.
 *
 * @package    qtype_shortanssimilarity
 * @copyright  2021 Yash Srivastava - VIP Research Group (ysrivast@ualberta.ca)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_shortanssimilarity;
defined('MOODLE_INTERNAL') || die();

/**
 * Adhoc task that calcualtes similarity between two multi-sentences.
 *
 * @copyright  2021 Yash Srivastava - VIP Research Group (ysrivast@ualberta.ca)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calculator extends \core\task\adhoc_task
{

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public function get_component()
    {
        return 'qtype_shortanssimilarity';
    }

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  float
     */
    public function execute()
    {
        global $DB;

        // Get the data.
        $data = $this->get_custom_data();

        // Prepare object to be sent to VIP Research's multi-sentence
        // short answer similarity.
        $json = array(
            'key' => $data->key,
            'target' => $data->target,
            'value' => $data->value,
            'maxBPM' => $data->maxBPM,
            'language' => $data->language
        );

        $json = json_encode($json);
        // Use file_get_get_contents and json_decode to capture response.
        $url = "https://ws-nlp.vipresearch.ca/bridge/v1/";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result == null) {
            $contents->similarity = 0;
        } else {
            $contents = json_decode($result);
        }

        // Update database with new values.
        $options = $DB->get_record('qtype_shortanssimilarity', array(
            'id' => $data->id
        ));
        $options->result = (string)$contents->similarity;
        $options->finished = 1;
        $DB->update_record('qtype_shortanssimilarity', $options);

        return true;
    }
}


