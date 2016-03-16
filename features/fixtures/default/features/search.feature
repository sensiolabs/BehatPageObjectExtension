@fixtures
Feature: Search
  In order to find lolcats
  As a Cat Lover
  I want to search the internetz

  Scenario: Searching for lolcats
    Given I visited the homepage
    Then I should not see the "Images" tab
