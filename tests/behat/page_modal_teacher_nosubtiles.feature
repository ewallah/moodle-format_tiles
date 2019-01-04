@format @format_tiles @page_modal_teacher_nosubtiles @javascript
Feature: Teacher can add a page to a course and open it with subtiles off

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | enablecompletion |
      | Course 1 | C1        | tiles  | 0             | 5           | 1                |
    And the following "activities" exist:
      | activity | name        | intro                 | course | idnumber | section | visible |
      | quiz     | Test quiz   | Test quiz description | C1     | quiz1    | 1       | 1       |
      | page     | Test page 1 | Test page description | C1     | page1    | 1       | 1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | config                 | value    | plugin       |
      | enablecompletion       | 1        | core         |
      | modalmodules           | page     | format_tiles |
      | modalresources         | pdf,html | format_tiles |
      | assumedatastoreconsent | 1        | format_tiles |
      | reopenlastsection      | 0        | format_tiles |
      | usejavascriptnav       | 1        | format_tiles |
      | jsmaxstoreditems       | 0        | format_tiles |
    And I log in as "teacher1"
    And format_tiles subtiles are off for course "Course 1"
    # We set jsmaxstoreditems to zero as otherwise when we switch between subtiles and tiles format we may not see an immediate change in display

  @javascript
  Scenario: Create and open new page using modal as teacher - subtiles off
    When I am on "Course 1" course homepage with editing mode on
    And I wait until the page is ready
    And I click on "#expand1" "css_element"
    And I add a "page" to section "1"
    And I wait until the page is ready
    And I set the following fields to these values:
      | Name                | Test page name 2                                     |
      | Description         | Test page description                                |
      | Page content        | Test page content                                    |
      | Completion tracking | Students can manually mark the activity as completed |
    And I click on "Save and display" "button"
    And I am on "Course 1" course homepage
    And I turn editing mode off
    And I click on tile "1"
    And I wait until the page is ready

    And I wait until activity "Test page name 2" exists in "non-subtile" format
    And I click format tiles activity "Test page name 2"
    And I wait "1" seconds
    And "Test page name 2" "dialogue" should be visible
    And "Test page content" "text" should be visible
    And "Close" "button" should exist in the "Test page name 2" "dialogue"
    And I click on "Close" "button"
    And I wait until the page is ready

    And I click on close button for tile "1"
    And "Test page content" "text" should not be visible
    And I wait "1" seconds
    And I log out
