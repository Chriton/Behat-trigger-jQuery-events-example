<?php
/**
 * Created by PhpStorm.
 * User: chriton
 */


use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;


use Behat\MinkExtension\Context\MinkContext;

// Load models
//use App\DocumentAPI;
//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    /**
     * Initializes context.
     * Every scenario gets its own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */

	const FILTER_FROM_TEXT = 'Perioadă de la: ';
	const FILTER_FROM_VALUE = '01-08-2015';

	const FILTER_TO_TEXT = 'Perioadă până la: ';
	const FILTER_TO_VALUE = '30-08-2015';

	const FILTER_DECISION_TEXT = 'Nr. Hotărâre: ';
	const FILTER_DECISION_VALUE = '1879';

    public function __construct(array $parameters)
    {
        //Initialize your context here
    }

	/**
	 * @param int $duration
	 */
	private function jqueryWait($duration = 1000)
	{
		//this will wait for all the animations on the page to finish

		$this->getSession()->wait($duration, '(
		document.readyState == \'complete\'					&&
		typeof $ != \'undefined\'							&&
		!$.active											&&
		$(\'#page\').css(\'display\') == \'block\'			&&
		$(\'.loading-mask\').css(\'display\') == \'none\'	&&
		$(\'.jstree-loading\').length == 0
		)');

		//"document.readyState == 'complete'",           // Page is ready
		//"typeof $ != 'undefined'",                     // jQuery is loaded
		//"!$.active",                                   // No ajax request is active
		//"$('#page').css('display') == 'block'",        // Page is displayed (no progress bar)
		//"$('.loading-mask').css('display') == 'none'", // Page is not loading (no black mask loading page)
		//"$('.jstree-loading').length == 0",            // Jstree has finished loading
	}

	/**
	 *
	 */
	public function CloseTheCalendar()
	{
		$this->jqueryWait();
		//trigger a jQuery hide event
		$script = "$('.datepicker').hide();";
		$this->getSession()->executeScript($script);
		$this->jqueryWait();
	}

	/**
	 * @param $id
	 */
	public function thenIClickOn($id)
	{
		$this->jqueryWait();
		//we trigger a jQuery click event on the element
		$script ='$("label:has(#' .$id . ')").trigger("click");';
		$this->getSession()->executeScript($script);
		$this->jqueryWait();
	}

	/**
	 * @Given /^I have a search with (\d+) filters$/
	 */
	public function iHaveASearchWithFilters($arg1)
	{
		$this->visit("/");
		$this->fillField("search_field","articol");
		$this->pressButton("Căutare avansată");

		$this->jqueryWait();
		$this->thenIClickOn("date_filter_type_interval");

		$this->fillField("filter_data_de_la",self::FILTER_FROM_VALUE);
		$this->CloseTheCalendar();

		$this->fillField("filter_data_pana_la",self::FILTER_TO_VALUE);
		$this->CloseTheCalendar();

		$this->fillField("filter_numar_document",self::FILTER_DECISION_VALUE);
		$this->jqueryWait();

		$this->pressButton("Caută");
		$this->jqueryWait();

		//check if the 3 filters are present
		$this->assertPageContainsText(self::FILTER_FROM_TEXT . self::FILTER_FROM_VALUE);
		$this->assertPageContainsText(self::FILTER_TO_TEXT . self::FILTER_TO_VALUE);
		$this->assertPageContainsText(self::FILTER_DECISION_TEXT . self::FILTER_DECISION_VALUE);
	}

	/**
	 * @When /^I delete (\d+) filter$/
	 */
	public function iDeleteFilter($arg1)
	{
		$this->jqueryWait();

		//trigger a jQuery click event
		$script ='$(".label_numar_document > a > svg > use").trigger("click");';
		$this->getSession()->executeScript($script);

		$this->jqueryWait();
		$this->assertPageNotContainsText(self::FILTER_DECISION_TEXT . self::FILTER_DECISION_VALUE);
	}

	/**
	 * @Then /^the other (\d+) filters should remain present$/
	 */
	public function theOtherFiltersShouldRemainPresent($arg1)
	{
		$this->assertPageContainsText(self::FILTER_FROM_TEXT . self::FILTER_FROM_VALUE);
		$this->assertPageContainsText(self::FILTER_TO_TEXT . self::FILTER_TO_VALUE);
	}
}
