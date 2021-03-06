<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace ShoppingFlux\API;

/**
 * Class Request
 * @package ShoppingFlux\API\Request
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class Request
{
    /**
     * @var \DOMDocument
     */
    protected $xml;

    /**
     * @var null|string
     */
    protected $errorMessage = null;

    public function __construct($rootName)
    {
        $rootName = htmlentities($rootName);

        $this->xml = new \DOMDocument("1.0");
        $this->xml->appendChild(new \DOMElement($rootName));
    }

    public function addOrder(array $data)
    {
        $tag = $this->xml->firstChild;
        $order = $tag->appendChild(new \DOMElement("Order"));

        foreach ($data as $title => $value) {
            $newNode = new \DOMElement($title, $value);
            $order->appendChild($newNode);
        }
    }

    public function isValid($XSDString)
    {
        $ok = false;
        try {
            $this->xml->schemaValidateSource($XSDString);
            $ok = true;
        } catch (\ErrorException $e) {
            $this->errorMessage = $e->getMessage();
        }

        return $ok;
    }

    public function getOrders()
    {
        $orders = [];
        $ordersNode = $this->xml->getElementsByTagName("Order");

        for ($i = 0; $i < $ordersNode->length; ++$i) {
            $orderNode = $ordersNode->item($i);

            $children = $orderNode->childNodes;

            $order = &$orders[];
            for ($j = 0; $j < $children->length; ++$j) {
                $order[$children->item($j)->nodeName] = $children->item($j)->nodeValue;
            }
        }

        return $orders;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __toString()
    {
        return $this->xml->C14N();
    }
}
