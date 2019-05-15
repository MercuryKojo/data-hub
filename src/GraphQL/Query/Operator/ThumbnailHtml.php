<?php
declare(strict_types=1);
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    Object
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Asset;

/**
 * Class ThumbnailHtml
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator
 */
class ThumbnailHtml extends AbstractOperator
{
    /**
     * @var string|null
     */
    private $thumbnailHtmlConfig;

    /**
     * ThumbnailHtml constructor.
     *
     * @param array $config
     * @param null $context
     */
    public function __construct(array $config = [], $context = null)
    {
        parent::__construct($config, $context);

        $this->thumbnailHtmlConfig = $config['thumbnailHtmlConfig'];
    }

    /**
     * @param \Pimcore\Model\Element\ElementInterface $element
     * @param ResolveInfo|null $resolveInfo
     *
     * @return \stdClass|null
     */
    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;
        if (!$this->thumbnailHtmlConfig) {
            return $result;
        }

        $children = $this->getChilds();

        if (!$children) {
            return $result;
        } else {
            $c = $children[0];

            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

            $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
            if ($childResult) {
                $result->value = null;
                if ($childResult->value instanceof Asset\Image || $childResult->value instanceof Asset\Video) {
                    $childValue = $result->value = $childResult->value;
                    $thumbnail = $childValue->getThumbnail($this->thumbnailHtmlConfig, false);
                    $result->value = $thumbnail->getHtml();
                }
            }
        }

        return $result;
    }
}
