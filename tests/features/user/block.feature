@api
Feature: Block users
  As a moderator I must be able to block users.

  Scenario: A moderator can block a user.
    Given users:
      | name         | roles          | mail                       |
      | Mod Murielle | Moderator      | mod.murielle@example.com     |
      | Liam Lego    |                | liam.lego@example.com     |
    # Search user
    And I am logged in as "Mod Murielle"
    Given I am on the homepage
    When I click "People"
    And I fill in "Name or email contains" with "Liam Lego"
    And I press the "Filter" button

    # Block the user
    Then I check "Liam Lego"
    Then I select "Block the selected user(s)" from "With selection"
    And I press the "Apply" button
    Then I should see the success message "Block the selected user(s) was applied to 1 item."
    Then I should see the success message "An e-mail has been send to the user to notify him on the change to his account."

    # Unblock the user
    Then I check "Liam Lego"
    Then I select "Unblock the selected user(s)" from "With selection"
    And I press the "Apply" button
    Then I should see the success message "Unblock the selected user(s) was applied to 1 item."
    Then I should see the success message "An e-mail has been send to the user to notify him on the change to his account."
