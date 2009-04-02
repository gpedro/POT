<?php
// Call POTTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "POTTest::main");
}

set_time_limit(0);

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'classes/OTS.php';

include('PHPUnit/Framework/TestListener.php');

class POTListener implements PHPUnit_Framework_TestListener
{
    const TERM_END = "\033[0;39m";
    const TERM_RED = "\033[1;31m";
    const TERM_GREEN = "\033[1;32m";
    const TERM_YELLOW = "\033[1;33m";
    const TERM_BLUE = "\033[1;34m";
    const TERM_MAGENTA = "\033[1;35m";
    const TERM_CYAN = "\033[1;36m";
    const TERM_WHITE = "\033[1;37m";
    const TERM_GRAY = "\033[1;30m";
    const TERM_RED_DARK = "\033[0;31m";
    const TERM_GREEN_DARK = "\033[0;32m";
    const TERM_YELLOW_DARK = "\033[0;33m";
    const TERM_BLUE_DARK = "\033[0;34m";
    const TERM_MAGENTA_DARK = "\033[0;35m";
    const TERM_CYAN_DARK = "\033[0;36m";
    const TERM_WHITE_DARK = "\033[0;37m";
    const TERM_GRAY_DARK = "\033[0;30m";
    const TERM_RED_BG = "\033[1;41m";
    const TERM_GREEN_BG = "\033[1;42m";
    const TERM_YELLOW_BG = "\033[1;43m";
    const TERM_BLUE_BG = "\033[1;44m";
    const TERM_MAGENTA_BG = "\033[1;45m";
    const TERM_CYAN_BG = "\033[1;46m";
    const TERM_WHITE_BG = "\033[1;47m";
    const TERM_BOLD = "\033[1;39m";

    private $error = false;

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->error = true;
        echo self::TERM_RED, 'error: ', $e->getMessage(), ' (took ', $time, 's).', self::TERM_END, "\n";
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->error = true;
        echo self::TERM_RED, 'failed: ', $e->getMessage(), ' (took ', $time, 's).', self::TERM_END, "\n";
    }

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->error = true;
        echo self::TERM_YELLOW, 'incomplete: ', $e->getMessage(), ' (took ', $time, 's).', self::TERM_END, "\n";
    }

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->error = true;
        echo self::TERM_YELLOW, 'skipped: ', $e->getMessage(), ' (took ', $time, 's).', self::TERM_END, "\n";
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        $this->error = false;
        echo $test->getName(), ': ';
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if(!$this->error)
        {
            echo self::TERM_GREEN, 'succeded (took ', $time, 's).', self::TERM_END, "\n";
        }
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        echo self::TERM_BOLD, 'PHP OTServ Toolkit test suite for phpUnit', self::TERM_END, "\n\n", 'Diagnostic info:', "\n", 'PHP version: ', self::TERM_BLUE, PHP_VERSION, self::TERM_END, "\n", 'Operating system: ', self::TERM_BLUE, PHP_OS, self::TERM_END, "\n", 'SAPI interface: ', self::TERM_BLUE, PHP_SAPI, self::TERM_END, "\n\n";
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }
}

/**
 * Test class for POT.
 * Generated by PHPUnit_Util_Skeleton on 2007-08-10 at 20:49:59.
 */
class POTTest extends PHPUnit_Framework_TestCase {
    private $ots;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        $results = new PHPUnit_Framework_TestResult();
        $results->addListener( new POTListener() );

        $suite = new PHPUnit_Framework_TestSuite("POTTest");
        $suite->run($results);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }
}

// Call POTTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "POTTest::main") {
    POTTest::main();
}
?>
