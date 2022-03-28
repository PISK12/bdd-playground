Feature: Adding Domain
  I want to adding new domain
  As Customer

  Scenario: Adding Domain
    Given the customer "Customer01"
    When adding domain "example.com" by "Customer01"
    Then the response status code should be 201

  Scenario: Customer Adding The Same Domain Twice
    Given the customer "Customer01"
    When adding domain "example.com" by "Customer01"
    And adding domain "example.com" by "Customer01"
    Then the response status code should be 500

  Scenario: Customer Adding The Subdomain For Exiting Domain Which He Owns
    Given the customer "Customer01"
    And the domain "example.com" by "Customer01"
    When adding domain "example.example.com" by "Customer01"
    Then the response status code should be 201

  Scenario: Customer Adding The Subdomain For Exiting Domain Which He Not Owns
    Given the customer "Customer01"
    And the customer "Customer02"
    And the domain "example.com" by "Customer01"
    When adding domain "example.example.com" by "Customer02"
    Then the response status code should be 500

  Scenario: Customer Adding The Subdomain For Exiting Domain Which He Not Owns But Domain Is Public
    Given the customer "Customer01"
    And the customer "Customer02"
    And the public domain "example.com" by "Customer01"
    When adding domain "example.example.com" by "Customer02"
    Then the response status code should be 201