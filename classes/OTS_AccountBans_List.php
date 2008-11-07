<?php

/**
 * @package POT
 * @version 0.1.5+SVN
 * @since 0.1.5+SVN
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 - 2008 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * List of account bans.
 * 
 * @package POT
 * @version 0.1.5+SVN
 * @since 0.1.5+SVN
 */
class OTS_AccountBans_List extends OTS_Bans_List
{
/**
 * Initializes list with account bans filtering.
 * 
 * @version 0.1.5+SVN
 * @since 0.1.5+SVN
 */
    public function __construct()
    {
        parent::__construct();

        // filters only account bans
        $filter = new OTS_SQLFilter();
        $filter->addFilter( new OTS_SQLField('type', 'bans'), POT::BAN_ACCOUNT);
        $this->setFilter($filter);
    }
}

?>
