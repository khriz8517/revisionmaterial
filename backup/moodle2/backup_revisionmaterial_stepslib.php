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
 * Define all the backup steps that will be used by the backup_revisionmaterial_activity_task
 *
 * @package    mod_revisionmaterial
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Structure step to backup one revisionmaterial activity
 */
class backup_revisionmaterial_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // Define each element separated.
        $revisionmaterial = new backup_nested_element('revisionmaterial', array('id'), array(
            'name', 'intro', 'introformat', 'numbering', 'navstyle',
            'customtitles', 'timecreated', 'timemodified'));
        $chapters = new backup_nested_element('chapters');
        $chapter = new backup_nested_element('chapter', array('id'), array(
            'pagenum', 'subchapter', 'title', 'content', 'contentformat',
            'hidden', 'timemcreated', 'timemodified', 'importsrc'));

        $tags = new backup_nested_element('chaptertags');
        $tag = new backup_nested_element('tag', array('id'), array('itemid', 'rawname'));

        $revisionmaterial->add_child($chapters);
        $chapters->add_child($chapter);

        // Define sources
        $revisionmaterial->set_source_table('revisionmaterial', array('id' => backup::VAR_ACTIVITYID));
        $chapter->set_source_table('revisionmaterial_chapters', array('revisionmaterialid' => backup::VAR_PARENTID));

        // Define file annotations
        $revisionmaterial->annotate_files('mod_revisionmaterial', 'intro', null); // This file area hasn't itemid
        $chapter->annotate_files('mod_revisionmaterial', 'chapter', 'id');

        $revisionmaterial->add_child($tags);
        $tags->add_child($tag);

        // All these source definitions only happen if we are including user info.
        if (core_tag_tag::is_enabled('mod_revisionmaterial', 'revisionmaterial_chapters')) {
            $tag->set_source_sql('SELECT t.id, ti.itemid, t.rawname
                                    FROM {tag} t
                                    JOIN {tag_instance} ti ON ti.tagid = t.id
                                   WHERE ti.itemtype = ?
                                     AND ti.component = ?
                                     AND ti.contextid = ?', array(
                backup_helper::is_sqlparam('revisionmaterial_chapters'),
                backup_helper::is_sqlparam('mod_revisionmaterial'),
                backup::VAR_CONTEXTID));
        }

        // Return the root element (revisionmaterial), wrapped into standard activity structure
        return $this->prepare_activity_structure($revisionmaterial);
    }
}
