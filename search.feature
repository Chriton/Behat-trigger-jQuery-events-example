	Feature: Search
		In order to see court orders
		As a user
		I want to be able to use a search engine located on the site's homepage

	@javascript @run
	Scenario: Deleting one of the filters doesn't affect the others
		Given I have a search with 3 filters
		When I delete 1 filter
		Then the other 2 filters should remain present
