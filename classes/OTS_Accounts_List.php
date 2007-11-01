<?php

/**#@+
 * @since 0.0.1
 */

/**
 * @package POT
 * @version 0.0.5
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * List of accounts.
 * 
 * @package POT
 * @version 0.0.5
 */
class OTS_Accounts_List extends OTS_Base_List
{
/**
 * Deletes account.
 * 
 * @version 0.0.5
 * @param OTS_Account $account Account to be deleted.
 * @deprecated 0.0.5 Use OTS_Account->delete().
 */
    public function deleteAccount(OTS_Account $account)
    {
        $this->db->query('DELETE FROM ' . $this->db->tableName('account') . ' WHERE ' . $this->db->fieldName('id') . ' = ' . $account->getId() );
    }

/**
 * Sets list parameters.
 * 
 * This method is called at object creation.
 * 
 * @version 0.0.5
 * @since 0.0.5
 */
    public function init()
    {
        $this->table = 'accounts';
        $this->class = 'Account';
    }
}

/**#@-*/

?>
