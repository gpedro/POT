<?php

/**#@+
 * @version 0.0.6+SVN
 * @since 0.0.6+SVN
 */

/**
 * Code in this file bases on oryginal OTServ binary format loading C++ code (fileloader.h, fileloader.cpp).
 * 
 * @package POT
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * Error during reading OTServ binary file.
 * 
 * @package POT
 */
class E_OTS_FileLoaderError extends E_OTS_ErrorCode
{
/**
 * Unsupported file version.
 */
    const ERROR_INVALID_FILE_VERSION = 1;
/**
 * Could not open file.
 */
    const ERROR_CAN_NOT_OPEN = 2;
/**
 * Unexpected end of file.
 */
    const ERROR_EOF = 4;
/**
 * {@link http://www.php.net/manual/en/function.fseek.php fseek()} operation failed.
 */
    const ERROR_SEEK_ERROR = 5;
/**
 * Attempted to execute operation on not opened file.
 */
    const ERROR_NOT_OPEN = 6;
/**
 * File corrupted.
 */
    const ERROR_INVALID_FORMAT = 8;
/**
 * {@link http://www.php.net/manual/en/function.ftell.php ftell()} operation failed.
 */
    const ERROR_TELL_ERROR = 9;
}

?>
