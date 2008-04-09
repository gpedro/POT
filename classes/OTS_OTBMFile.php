<?php

/**#@+
 * @version 0.0.6
 * @since 0.0.6
 */

/**
 * Code in this file bases on oryginal OTServ OTBM format loading C++ code (iomapotbm.h, iomapotbm.cpp).
 * 
 * @package POT
 * @version 0.1.3+SVN
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 - 2008 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 * @todo future: Complete OTBM support: link tiles with items and spawns.
 * @todo future: Spawns support.
 */

/**
 * OTBM format reader.
 * 
 * @package POT
 * @version 0.1.3+SVN
 * @property-read OTS_HousesList $housesList Houses list loaded from associated houses file.
 * @property-read int $width Map width.
 * @property-read int $height Map height.
 * @property-read string $description Map description.
 * @tutorial POT/data_directory.pkg#towns
 * @example examples/otbm.php otbm.php
 */
class OTS_OTBMFile extends OTS_FileLoader implements IteratorAggregate, Countable, ArrayAccess
{
/**
 * Description attribute.
 */
    const OTBM_ATTR_DESCRIPTION = 1;
/**
 * External file.
 */
    const OTBM_ATTR_EXT_FILE = 2;
/**
 * Tile flags.
 */
    const OTBM_ATTR_TILE_FLAGS = 3;
/**
 * Action ID.
 */
    const OTBM_ATTR_ACTION_ID = 4;
/**
 * Unique ID.
 */
    const OTBM_ATTR_UNIQUE_ID = 5;
/**
 * Text.
 */
    const OTBM_ATTR_TEXT = 6;
/**
 * Description.
 */
    const OTBM_ATTR_DESC = 7;
/**
 * Teleport destination.
 */
    const OTBM_ATTR_TELE_DEST = 8;
/**
 * Item.
 */
    const OTBM_ATTR_ITEM = 9;
/**
 * Depot ID.
 */
    const OTBM_ATTR_DEPOT_ID = 10;
/**
 * External spawns file.
 */
    const OTBM_ATTR_EXT_SPAWN_FILE = 11;
/**
 * Rune changes amount.
 */
    const OTBM_ATTR_RUNE_CHARGES = 12;
/**
 * External houses file.
 */
    const OTBM_ATTR_EXT_HOUSE_FILE = 13;
/**
 * ID of doors.
 */
    const OTBM_ATTR_HOUSEDOORID = 14;

/**
 * Root node.
 */
    const OTBM_NODE_ROOTV1 = 1;
/**
 * Map data container.
 */
    const OTBM_NODE_MAP_DATA = 2;
/**
 * Item definition.
 */
    const OTBM_NODE_ITEM_DEF = 3;
/**
 * Map tiles fragment.
 */
    const OTBM_NODE_TILE_AREA = 4;
/**
 * Single tile.
 */
    const OTBM_NODE_TILE = 5;
/**
 * Item.
 */
    const OTBM_NODE_ITEM = 6;
/**
 * Tile.
 */
    const OTBM_NODE_TILE_SQUARE = 7;
/**
 * Tile reference.
 */
    const OTBM_NODE_TILE_REF = 8;
/**
 * Spawns container.
 */
    const OTBM_NODE_SPAWNS = 9;
/**
 * Spawn.
 */
    const OTBM_NODE_SPAWN_AREA = 10;
/**
 * Monster.
 */
    const OTBM_NODE_MONSTER = 11;
/**
 * Towns container.
 */
    const OTBM_NODE_TOWNS = 12;
/**
 * Town.
 */
    const OTBM_NODE_TOWN = 13;
/**
 * Tile of house.
 */
    const OTBM_NODE_HOUSETILE = 14;

/**
 * Map width.
 * 
 * @var int
 */
    private $width;

/**
 * Map height.
 * 
 * @var int
 */
    private $height;

/**
 * Map description.
 * 
 * @var string
 */
    private $description = '';

/**
 * List of towns.
 * 
 * @var array
 */
    private $towns = array();

/**
 * Temple positions.
 * 
 * @var array
 */
    private $temples = array();

/**
 * Directory path.
 * 
 * @var string
 */
    private $directory;

/**
 * External houses file.
 * 
 * @var OTS_HousesList
 */
    private $housesList;

/**
 * Magic PHP5 method.
 * 
 * <p>
 * Allows object unserialisation.
 * </p>
 * 
 * @throws E_OTS_FileLoaderError When error occurs during file operation.
 */
    public function __wakeup()
    {
        // loads map info from recovered root node
        $this->parse();
    }

/**
 * Loads OTBM file content.
 * 
 * @version 0.1.0
 * @param string $file Filename.
 * @throws E_OTS_FileLoaderError When error occurs during file operation.
 * @throws E_OTS_OutOfBuffer When there is read attemp after end of stream.
 */
    public function loadFile($file)
    {
        // loads file structure
        parent::loadFile($file);

        // saves directory path for external files
        $this->directory = dirname($file);

        // parses loaded file
        $this->parse();
    }

/**
 * Parses loaded file.
 * 
 * @version 0.1.0
 * @throws E_OTS_FileLoaderError When error occurs during file operation.
 * @throws E_OTS_OutOfBuffer When there is read attemp after end of stream.
 */
    private function parse()
    {
        // root node header
        $version = $this->root->getLong();
        $this->width = $this->root->getShort();
        $this->height = $this->root->getShort();
        $majorVersionItems = $this->root->getLong();
        $minorVersionItems = $this->root->getLong();

        // checks version of root node
        if($version != 0)
        {
            throw new E_OTS_OTBMError(E_OTS_OTBMError::LOADMAPERROR_OUTDATEDHEADER);
        }

        // reads root's first child
        $node = $this->root->getChild();

        // it should be OTBM_NODE_MAP_DATA
        if( $node->getType() != self::OTBM_NODE_MAP_DATA)
        {
            throw new E_OTS_OTBMError(E_OTS_OTBMError::LOADMAPERROR_UNKNOWNNODETYPE);
        }

        // reads map attributes
        while( $node->isValid() )
        {
            switch( $node->getChar() )
            {
                // description text
                case self::OTBM_ATTR_DESCRIPTION:
                    $this->description .= $node->getString() . "\n";
                    break;

                // external houses file
                case self::OTBM_ATTR_EXT_HOUSE_FILE:
                    $this->housesList = new OTS_HousesList($this->directory . '/' . $node->getString() );
                    break;

                // external spawns file
                case self::OTBM_ATTR_EXT_SPAWN_FILE:
                    // just we need to skip known attributes
                    $node->getString();
                    break;

                // unsupported attribute
                default:
                    throw new E_OTS_OTBMError(E_OTS_OTBMError::LOADMAPERROR_UNKNOWNNODETYPE);
            }
        }

        $node = $node->getChild();

        // reads map nodes
        while($node)
        {
            switch( $node->getType() )
            {
                // map definition part
                case self::OTBM_NODE_TILE_AREA:
                    // reads base X, Y and Z coords
                    $baseX = $node->getShort();
                    $baseY = $node->getShort();
                    $z = $node->getChar();

                    $tile = $node->getChild();

                    // reads houses tiles
                    while($tile)
                    {
                        // we dont need other tiles at the moment in POT, feel free to add it's suport on yourself
                        if( $tile->getType() == self::OTBM_NODE_HOUSETILE)
                        {
                            // reads tile coords
                            $offsetX = $tile->getChar();
                            $offsetY = $tile->getChar();
                            $coords = new OTS_MapCoords($baseX + $offsetX, $baseY + $offsetY, $z);

                            // reads house by it's ID
                            $house = $this->housesList->getHouse( $tile->getLong() );
                            $house->addTile($coords);
                        }

                        $tile = $tile->getNext();
                    }
                    break;

                // list of towns on map
                case self::OTBM_NODE_TOWNS:
                    $town = $node->getChild();

                    // reads all towns
                    while($town)
                    {
                        // checks if it's town node
                        if( $town->getType() != self::OTBM_NODE_TOWN)
                        {
                            throw new E_OTS_OTBMError(E_OTS_OTBMError::LOADMAPERROR_UNKNOWNNODETYPE);
                        }

                        // reads basic town info
                        $id = $town->getLong();
                        $this->towns[$id] = $town->getString();

                        // temple coords
                        $x = $town->getShort();
                        $y = $town->getShort();
                        $z = $town->getChar();
                        $this->temples[$id] = new OTS_MapCoords($x, $y, $z);

                        $town = $town->getNext();
                    }
                    break;

                // unknown type
                default:
                    throw new E_OTS_OTBMError(E_OTS_OTBMError::LOADMAPERROR_UNKNOWNNODETYPE);
            }

            $node = $node->getNext();
        }
    }

/**
 * Loads map's houses list.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @return OTS_HousesList Houses from external file.
 */
    public function getHousesList()
    {
        return $this->housesList;
    }

/**
 * Returns map width.
 * 
 * @return int Map width.
 */
    public function getWidth()
    {
        return $this->width;
    }

/**
 * Returns map height.
 * 
 * @return int Map height.
 */
    public function getHeight()
    {
        return $this->height;
    }

/**
 * Returns map description.
 * 
 * @return string Map description.
 */
    public function getDescription()
    {
        return $this->description;
    }

/**
 * Checks if given town ID exists on list.
 * 
 * @version 0.1.3+SVN
 * @since 0.1.3+SVN
 * @param int $id ID.
 * @return bool If town is set then true.
 */
    public function hasTownId($id)
    {
        return isset($this->towns[$id]);
    }

/**
 * Returns town's ID.
 * 
 * @version 0.1.3+SVN
 * @param string $name Town.
 * @return int ID.
 * @throws OutOfBoundsException If not found.
 */
    public function getTownID($name)
    {
        $id = array_search($name, $this->towns);

        if($id === false)
        {
            throw new OutOfBoundsException();
        }

        return $id;
    }

/**
 * Checks if given town name exists on list.
 * 
 * @version 0.1.3+SVN
 * @since 0.1.3+SVN
 * @param string $name Town.
 * @return bool If town is set then true.
 */
    public function hasTownName($name)
    {
        return array_search($name, $this->towns) !== false;
    }

/**
 * Returns name of given town's ID.
 * 
 * @version 0.1.3+SVN
 * @param int $id Town ID.
 * @return string Name.
 * @throws OutOfBoundsException If not found.
 */
    public function getTownName($id)
    {
        if( isset($this->towns[$id]) )
        {
            return $this->towns[$id];
        }

        throw new OutOfBoundsException();
    }

/**
 * @return array List of towns.
 * @deprecated 0.1.0 Use this class object as array for iterations, counting and methods for field fetching.
 */
    public function getTownsList()
    {
        return $this->towns;
    }

/**
 * Returns town's temple position.
 * 
 * @param int $id Town id.
 * @return OTS_MapCoords|bool Point on map (false if not found).
 */
    public function getTownTemple($id)
    {
        if( isset($this->temples[$id]) )
        {
            return $this->temples[$id];
        }
        else
        {
            return false;
        }
    }

/**
 * Returns amount of towns loaded.
 * 
 * @version 0.0.8
 * @since 0.0.8
 * @return int Count of towns.
 */
    public function count()
    {
        return count($this->towns);
    }

/**
 * @version 0.0.8
 * @since 0.0.8
 * @return string Town name.
 * @deprecated 0.1.0 Use getIterator().
 */
    public function current()
    {
        return current($this->towns);
    }

/**
 * @version 0.0.8
 * @since 0.0.8
 * @deprecated 0.1.0 Use getIterator().
 */
    public function next()
    {
        next($this->towns);
    }

/**
 * @version 0.0.8
 * @since 0.0.8
 * @return int Current position key.
 * @deprecated 0.1.0 Use getIterator().
 */
    public function key()
    {
        return key($this->towns);
    }

/**
 * @version 0.0.8
 * @since 0.0.8
 * @return bool If iterator has anything more.
 * @deprecated 0.1.0 Use getIterator().
 */
    public function valid()
    {
        return key($this->towns) !== null;
    }

/**
 * @version 0.0.8
 * @since 0.0.8
 * @deprecated 0.1.0 Use getIterator().
 */
    public function rewind()
    {
        reset($this->towns);
    }

/**
 * Returns iterator handle for loops.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @return ArrayIterator Towns list iterator.
 */
    public function getIterator()
    {
        return new ArrayIterator($this->towns);
    }

/**
 * Checks if given element exists.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @param string|int $offset Array key.
 * @return bool True if it's set.
 */
    public function offsetExists($offset)
    {
        // integer key
        if( is_int($offset) )
        {
            return isset($this->towns[$offset]);
        }
        // town name
        else
        {
            return $this->getTownId($offset) !== false;
        }
    }

/**
 * Returns item from given position.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @param string|int $offset Array key.
 * @return mixed If key is an integer (type-sensitive!) then returns town name. If it's a string then return associated ID found by town name. False if offset is not set.
 */
    public function offsetGet($offset)
    {
        // integer key
        if( is_int($offset) )
        {
            if( isset($this->towns[$offset]) )
            {
                return $this->towns[$offset];
            }
            // keys is not set
            else
            {
                return false;
            }
        }
        // town name
        else
        {
            return $this->getTownId($offset);
        }
    }

/**
 * This method is implemented for ArrayAccess interface. In fact you can't write/append to towns list. Any call to this method will cause {@link E_OTS_ReadOnly E_OTS_ReadOnly} raise.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @param string|int $offset Array key.
 * @param mixed $value Field value.
 * @throws E_OTS_ReadOnly Always - this class is read-only.
 */
    public function offsetSet($offset, $value)
    {
        throw new E_OTS_ReadOnly();
    }

/**
 * This method is implemented for ArrayAccess interface. In fact you can't write/append to towns list. Any call to this method will cause {@link E_OTS_ReadOnly E_OTS_ReadOnly} raise.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @param string|int $offset Array key.
 * @throws E_OTS_ReadOnly Always - this class is read-only.
 */
    public function offsetUnset($offset)
    {
        throw new E_OTS_ReadOnly();
    }

/**
 * Magic PHP5 method.
 * 
 * @version 0.1.0
 * @since 0.1.0
 * @param string $name Property name.
 * @return mixed Property value.
 * @throws OutOfBoundsException For non-supported properties.
 */
    public function __get($name)
    {
        switch($name)
        {
            case 'housesList':
                return $this->getHousesList();

            case 'width':
                return $this->getWidth();

            case 'height':
                return $this->getHeight();

            case 'description':
                return $this->getDescription();

            default:
                throw new OutOfBoundsException();
        }
    }

/**
 * Returns string representation of object.
 * 
 * <p>
 * If any display driver is currently loaded then it uses it's method.
 * </p>
 * 
 * @version 0.1.3+SVN
 * @since 0.1.3+SVN
 * @return string String representation of object.
 */
    public function __toString()
    {
        $ots = POT::getInstance();

        // checks if display driver is loaded
        if( $ots->isDataDisplayDriverLoaded() )
        {
            return $ots->getDataDisplayDriver()->displayOTBMMap($this);
        }

        return (string) $this->count();
    }
}

/**#@-*/

?>
