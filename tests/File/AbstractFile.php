<?php

namespace WsdlToPhp\PackageGenerator\Tests\File;

use WsdlToPhp\PackageGenerator\File\AbstractFile as File;
use WsdlToPhp\PackageGenerator\Generator\Generator;
use WsdlToPhp\PackageGenerator\Parser\SoapClient\Structs as StructsParser;
use WsdlToPhp\PackageGenerator\Parser\SoapClient\Functions as FunctionsParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagAttribute as TagAttributeParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagComplexType as TagComplexTypeParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagDocumentation as TagDocumentationParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagElement as TagElementParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagEnumeration as TagEnumerationParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagExtension as TagExtensionParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagHeader as TagHeaderParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagImport as TagImportParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagInclude as TagIncludeParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagInput as TagInputParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagList as TagListParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagOutput as TagOutputParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagRestriction as TagRestrictionParser;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagUnion as TagUnionParser;
use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\Model\AbstractModel;
use WsdlToPhp\PackageGenerator\ConfigurationReader\GeneratorOptions;

abstract class AbstractFile extends TestCase
{
    /**
     * @return Generator
     */
    public static function bingGeneratorInstance($reset = true, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlBingPath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function actonGeneratorInstance($reset = true, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlActonPath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function portalGeneratorInstance($reset = true)
    {
        return self::getInstance(self::wsdlPortalPath(), $reset);
    }
    /**
     * @return Generator
     */
    public static function reformaGeneratorInstance($reset = true)
    {
        return self::getInstance(self::wsdlReformaPath(), $reset);
    }
    /**
     * @return Generator
     */
    public static function queueGeneratorInstance($reset = true)
    {
        return self::getInstance(self::wsdlQueuePath(), $reset);
    }
    /**
     * @return Generator
     */
    public static function omnitureGeneratorInstance($reset = true, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlOmniturePath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function odigeoGeneratorInstance($reset = true)
    {
        return self::getInstance(self::wsdlOdigeoPath(), $reset);
    }
    /**
     * @return Generator
     */
    public static function payPalGeneratorInstance($reset = true, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlPayPalPath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function wcfGeneratorInstance($reset = false, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlWcfPath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function yandexDirectApiAdGroupsGeneratorInstance($reset = false, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlYandexDirectApiAdGroupsPath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function yandexDirectApiCampaignsGeneratorInstance($reset = false, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlYandexDirectApiCampaignsPath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function yandexDirectApiLiveGeneratorInstance($reset = false, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlYandexDirectApiLivePath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function docDataPaymentsGeneratorInstance($reset = false, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlDocDataPaymentsPath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function deliveryServiceInstance($reset = false, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlDeliveryServicePath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function orderContractInstance($reset = false, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlOrderContractPath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function whlInstance($reset = false, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        return self::getInstance(self::wsdlWhlPath(), $reset, $gatherMethods);
    }
    /**
     * @return Generator
     */
    public static function ewsInstance()
    {
        return self::getInstanceFromSerializedJson('parsed_ews');
    }
    /**
     * @param string $wsdl
     * @return Generator
     */
    public static function getInstance($wsdl, $reset = true, $gatherMethods = GeneratorOptions::VALUE_START)
    {
        AbstractModel::purgeUniqueNames();
        AbstractModel::purgePhpReservedKeywords();
        $g = parent::getInstance($wsdl, $reset)
            ->setOptionPrefix('Api')
            ->setOptionAddComments(array(
                'release' => '1.1.0',
            ))
            ->setOptionCategory(GeneratorOptions::VALUE_CAT)
            ->setOptionGatherMethods($gatherMethods);
        self::applyParsers($g, $wsdl);
        return $g;
    }
    /**
     * @param string $id
     * @return Generator
     */
    public static function getInstanceFromSerializedJson($id)
    {
        return Generator::instanceFromSerializedJson(file_get_contents(self::getTestDirectory() . $id . '.json'));;
    }
    /**
     * @param Generator $generator
     * @param string $wsdlPath
     */
    private static function applyParsers(Generator $generator, $wsdlPath)
    {
        $parsers = array(
            new FunctionsParser($generator),
            new StructsParser($generator),
            new TagIncludeParser($generator),
            new TagImportParser($generator),
            new TagAttributeParser($generator),
            new TagComplexTypeParser($generator),
            new TagDocumentationParser($generator),
            new TagElementParser($generator),
            new TagEnumerationParser($generator),
            new TagExtensionParser($generator),
            new TagHeaderParser($generator),
            new TagInputParser($generator),
            new TagOutputParser($generator),
            new TagRestrictionParser($generator),
            new TagUnionParser($generator),
            new TagListParser($generator),
        );
        foreach ($parsers as $parser) {
            $parser->parse();
        }
    }
    /**
     * @param string $valid
     * @param File $file
     */
    protected function assertSameFileContent($valid, File $file, $fileExtension = 'php')
    {
        if (!is_file($file->getFileName())) {
            return $this->assertFalse(true, sprintf('Generated file "%s" could not be found', $file->getFileName()));
        }
        $validContent = file_get_contents(sprintf('%s%s.%s', self::getTestDirectory(), $valid, $fileExtension));
        $validContent = str_replace('__WSDL_URL__', $file->getGenerator()->getWsdl()->getName(), $validContent);
        $toBeValidatedContent = file_get_contents($file->getFileName());
        $this->assertSame($validContent, $toBeValidatedContent);
        unlink($file->getFileName());
    }
}
