<?php

require_once DOKU_INC.'lib/plugins/odt/ODT/styles/ODTStyle.php';

/**
 * Tests to ensure functionality of the ODTParagraphStyle class.
 *
 * @group plugin_odt
 * @group plugins
 */
class plugin_odt_paragraphstyle_test extends DokuWikiTest {
    public function setUp() {
        $this->pluginsEnabled[] = 'odt';
        parent::setUp();
    }

    /**
     * Test ODT XML style definition import.
     */
    public function test_simple_odt_import() {
        $xml_code = '<style:style style:name="Heading" style:family="paragraph" style:parent-style-name="Standard" style:next-style-name="Text_20_body" style:class="text">
                         <style:paragraph-properties fo:margin-top="0.423cm" fo:margin-bottom="0.212cm" fo:keep-with-next="always"/>
                         <style:text-properties style:font-name="Bitstream Vera Sans1" fo:font-size="14pt" style:font-name-asian="Bitstream Vera Sans2" style:font-size-asian="14pt" style:font-name-complex="Bitstream Vera Sans2" style:font-size-complex="14pt"/>
                     </style:style>';

        $style = ODTStyle::importODTStyle($xml_code);
        $this->assertNotNull($style);

        $this->assertEquals($style->getFamily(), 'paragraph');
        $this->assertEquals($style->getProperty('style-name'), 'Heading');
        $this->assertEquals($style->getProperty('style-family'), 'paragraph');
        $this->assertEquals($style->getProperty('style-parent'), 'Standard');
        $this->assertEquals($style->getProperty('style-next'), 'Text_20_body');
        $this->assertEquals($style->getProperty('style-class'), 'text');
        $this->assertEquals($style->getProperty('margin-top'), '0.423cm');
        $this->assertEquals($style->getProperty('margin-bottom'), '0.212cm');
        $this->assertEquals($style->getProperty('keep-with-next'), 'always');
        $this->assertEquals($style->getProperty('font-name'), 'Bitstream Vera Sans1');
        $this->assertEquals($style->getProperty('font-size'), '14pt');
        $this->assertEquals($style->getProperty('font-name-asian'), 'Bitstream Vera Sans2');
        $this->assertEquals($style->getProperty('font-size-asian'), '14pt');
        $this->assertEquals($style->getProperty('font-name-complex'), 'Bitstream Vera Sans2');
        $this->assertEquals($style->getProperty('font-size-complex'), '14pt');
    }


    /**
     * Test ODT XML style definition import and conversion to string.
     */
    public function test_import_and_to_string() {
        $xml_code = '<style:style style:name="Heading" style:family="paragraph" style:parent-style-name="Standard" style:next-style-name="Text_20_body" style:class="text">
                         <style:paragraph-properties fo:margin-top="0.423cm" fo:margin-bottom="0.212cm" fo:keep-with-next="always"/>
                         <style:text-properties style:font-name="Bitstream Vera Sans1" fo:font-size="14pt" style:font-name-asian="Bitstream Vera Sans2" style:font-size-asian="14pt" style:font-name-complex="Bitstream Vera Sans2" style:font-size-complex="14pt"/>
                     </style:style>';
        // The order of attributes will change! This is OK.
        $expected  = '<style:style style:name="Heading" style:parent-style-name="Standard" style:class="text" style:next-style-name="Text_20_body" style:family="paragraph" >'."\n";
        $expected .= '<style:paragraph-properties fo:margin-top="0.423cm" fo:margin-bottom="0.212cm" fo:keep-with-next="always" />'."\n";
        $expected .= '<style:text-properties fo:font-size="14pt" style:font-size-asian="14pt" style:font-size-complex="14pt" style:font-name="Bitstream Vera Sans1" style:font-name-asian="Bitstream Vera Sans2" style:font-name-complex="Bitstream Vera Sans2" />'."\n";
        $expected .= '</style:style>'."\n";

        $style = ODTStyle::importODTStyle($xml_code);
        $this->assertNotNull($style);

        $style_string = $style->toString();

        $this->assertEquals($expected, $style_string);
    }

    /**
     * Test set and get of a property.
     */
    public function test_set_and_get() {
        $xml_code = '<style:style style:name="Heading" style:family="paragraph" style:parent-style-name="Standard" style:next-style-name="Text_20_body" style:class="text">
                         <style:paragraph-properties fo:margin-top="0.423cm" fo:margin-bottom="0.212cm" fo:keep-with-next="always"/>
                         <style:text-properties style:font-name="Bitstream Vera Sans1" fo:font-size="14pt" style:font-name-asian="Bitstream Vera Sans2" style:font-size-asian="14pt" style:font-name-complex="Bitstream Vera Sans2" style:font-size-complex="14pt"/>
                     </style:style>';

        $style = ODTStyle::importODTStyle($xml_code);
        $this->assertNotNull($style);

        $style->setProperty('margin-top', '999cm');

        $this->assertEquals($style->getProperty('margin-top'), '999cm');
    }

    /**
     * Test properties import and conversion to string.
     */
    public function test_import_properties_and_to_string() {
        $properties = array();
        $properties ['style-name']        = 'Heading';
        $properties ['style-parent']      = 'Standard';
        $properties ['style-class']       = 'text';
        $properties ['style-family']      = 'paragraph';
        $properties ['style-next']        = 'Text_20_body';
        $properties ['margin-top']        = '0.423cm';
        $properties ['margin-bottom']     = '0.212cm';
        $properties ['keep-with-next']    = 'always';
        $properties ['font-size']         = '14pt';
        $properties ['font-size-asian']   = '14pt';
        $properties ['font-size-complex'] = '14pt';
        $properties ['font-name']         = 'Bitstream Vera Sans1';
        $properties ['font-name-asian']   = 'Bitstream Vera Sans2';
        $properties ['font-name-complex'] = 'Bitstream Vera Sans2';
        
        $expected  = '<style:style style:name="Heading" style:parent-style-name="Standard" style:class="text" style:next-style-name="Text_20_body" style:family="paragraph" >'."\n";
        $expected .= '<style:paragraph-properties fo:margin-top="0.423cm" fo:margin-bottom="0.212cm" fo:keep-with-next="always" />'."\n";
        $expected .= '<style:text-properties fo:font-size="14pt" style:font-size-asian="14pt" style:font-size-complex="14pt" style:font-name="Bitstream Vera Sans1" style:font-name-asian="Bitstream Vera Sans2" style:font-name-complex="Bitstream Vera Sans2" />'."\n";
        $expected .= '</style:style>'."\n";

        $style = new ODTParagraphStyle();
        $this->assertNotNull($style);

        $style->importProperties($properties, NULL);
        $style_string = $style->toString();

        $this->assertEquals($expected, $style_string);
    }

    /**
     * Test default ODT XML style definition import and conversion to string.
     */
    public function test_import_and_to_string_default() {
        $xml_code = '<style:default-style style:name="Heading" style:family="paragraph" style:parent-style-name="Standard" style:next-style-name="Text_20_body" style:class="text">
                         <style:paragraph-properties fo:margin-top="0.423cm" fo:margin-bottom="0.212cm" fo:keep-with-next="always"/>
                         <style:text-properties style:font-name="Bitstream Vera Sans1" fo:font-size="14pt" style:font-name-asian="Bitstream Vera Sans2" style:font-size-asian="14pt" style:font-name-complex="Bitstream Vera Sans2" style:font-size-complex="14pt"/>
                     </style:default-style>';
        // The order of attributes will change! This is OK.
        $expected  = '<style:default-style style:name="Heading" style:parent-style-name="Standard" style:class="text" style:next-style-name="Text_20_body" style:family="paragraph" >'."\n";
        $expected .= '<style:paragraph-properties fo:margin-top="0.423cm" fo:margin-bottom="0.212cm" fo:keep-with-next="always" />'."\n";
        $expected .= '<style:text-properties fo:font-size="14pt" style:font-size-asian="14pt" style:font-size-complex="14pt" style:font-name="Bitstream Vera Sans1" style:font-name-asian="Bitstream Vera Sans2" style:font-name-complex="Bitstream Vera Sans2" />'."\n";
        $expected .= '</style:default-style>'."\n";

        $style = ODTStyle::importODTStyle($xml_code);
        $this->assertNotNull($style);

        $style_string = $style->toString();

        $this->assertEquals(true, $style->isDefault());
        $this->assertEquals($expected, $style_string);
    }

    /**
     * Test setProperty() and toString().
     * This is a test case for issue #120.
     */
    public function test_set_and_to_string() {
        $properties = array();
        $properties ['style-name']        = 'Heading';
        $properties ['style-parent']      = 'Standard';
        $properties ['style-class']       = 'text';
        $properties ['style-next']        = 'Text_20_body';
        $properties ['margin-top']        = '0.423cm';
        $properties ['margin-bottom']     = '0.212cm';
        $properties ['keep-with-next']    = 'always';
        $properties ['font-size']         = '14pt';
        $properties ['font-size-asian']   = '14pt';
        $properties ['font-size-complex'] = '14pt';
        $properties ['font-name']         = 'Bitstream Vera Sans1';
        $properties ['font-name-asian']   = 'Bitstream Vera Sans2';
        $properties ['font-name-complex'] = 'Bitstream Vera Sans2';
        
        $expected  = '<style:style style:name="Heading" style:parent-style-name="Standard" style:class="text" style:next-style-name="Text_20_body" style:family="paragraph" >'."\n";
        $expected .= '<style:paragraph-properties fo:margin-top="0.423cm" fo:margin-bottom="0.212cm" fo:keep-with-next="always" />'."\n";
        $expected .= '<style:text-properties fo:font-size="14pt" style:font-size-asian="14pt" style:font-size-complex="14pt" style:font-name="Bitstream Vera Sans1" style:font-name-asian="Bitstream Vera Sans2" style:font-name-complex="Bitstream Vera Sans2" />'."\n";
        $expected .= '</style:style>'."\n";

        $style = new ODTParagraphStyle();
        $this->assertNotNull($style);

        foreach ($properties as $key => $value) {
            $style->setProperty($key, $value);
        }
        $style_string = $style->toString();

        // We should have the following elements:
        // style:style, style:paragraph-properties, style:text-properties
        $style_style = XMLUtil::getElementOpenTag('style:style', $style_string);
        $this->assertNotNull($style_style);
        $paragraph_props = XMLUtil::getElementOpenTag('style:paragraph-properties', $style_string);
        $this->assertNotNull($paragraph_props);
        $text_props = XMLUtil::getElementOpenTag('style:text-properties', $style_string);
        $this->assertNotNull($text_props);

        // Check attribute values of element "style:style", see $expected
        // Remark: attribute 'style:family' must always be present even if it was not set
        $attributes = array();
        $found = XMLUtil::getAttributes($attributes, $style_style);
        $this->assertEquals(5, $found);
        $this->assertEquals('Heading', $attributes['style:name']);
        $this->assertEquals('Standard', $attributes['style:parent-style-name']);
        $this->assertEquals('text', $attributes['style:class']);
        $this->assertEquals('Text_20_body', $attributes['style:next-style-name']);
        $this->assertEquals('paragraph', $attributes['style:family']);

        // Check attribute values of element "style:paragraph-properties", see $expected
        $attributes = array();
        $found = XMLUtil::getAttributes($attributes, $paragraph_props);
        $this->assertEquals(3, $found);
        $this->assertEquals('0.423cm', $attributes['fo:margin-top']);
        $this->assertEquals('0.212cm', $attributes['fo:margin-bottom']);
        $this->assertEquals('always', $attributes['fo:keep-with-next']);

        // Check attribute values of element "style:text-properties", see $expected
        $attributes = array();
        $found = XMLUtil::getAttributes($attributes, $text_props);
        $this->assertEquals(6, $found);
        $this->assertEquals('14pt', $attributes['fo:font-size']);
        $this->assertEquals('14pt', $attributes['style:font-size-asian']);
        $this->assertEquals('14pt', $attributes['style:font-size-complex']);
        $this->assertEquals('Bitstream Vera Sans1', $attributes['style:font-name']);
        $this->assertEquals('Bitstream Vera Sans2', $attributes['style:font-name-asian']);
        $this->assertEquals('Bitstream Vera Sans2', $attributes['style:font-name-complex']);
    }
}
