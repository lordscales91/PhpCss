<?php
/**
* An visitor that compiles the AST into a xpath expression
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Ast
*/

/**
* An visitor that compiles the AST into a xpath expression
*
* @package PhpCss
* @subpackage Ast
*/
class PhpCssAstVisitorXpath extends PhpCssAstVisitorOverload {

  private $_buffer = '';

  /**
  * Clear the visitor object to visit another selector group
  */
  public function clear() {
    $this->_buffer = '';
  }

  /**
  * Return the collected selector string
  */
  public function __toString() {
    return $this->_buffer;
  }

  /**
  * Validate the buffer before vistiting a PhpCssAstSelectorGroup.
  * If the buffer already contains data, throw an exception.
  *
  * @throws LogicException
  * @param PhpCssAstSelectorGroup $group
  * @return boolean
  */
  public function visitEnterSelectorSequenceGroup(PhpCssAstSelectorGroup $group) {
    if (!empty($this->_buffer)) {
      throw new LogicException(
        sprintf(
          'Visitor buffer already contains data, can not visit "%s"',
          get_class($group)
        )
      );
    }
    return TRUE;
  }

  /**
  * If here is already data in the buffer, add a separator before starting the next.
  *
  * @param PhpCssAstSelectorSequence $sequence
  * @return boolean
  */
  public function visitEnterSelectorSequence(PhpCssAstSelectorSequence $sequence) {
    if (!empty($this->_buffer)) {
      $this->_buffer .= '|';
    }
    $this->_buffer .= '*';
    return TRUE;
  }

  /**
  * Output the type selector to the buffer
  *
  * @param PhpCssAstSelectorSimpleType $type
  * @return boolean
  */
  public function visitSelectorSimpleType(PhpCssAstSelectorSimpleType $type) {
    if (!empty($type->namespacePrefix) && $type->namespacePrefix != '*') {
      $this->_buffer .= '[name() = "'.$type->namespacePrefix.':'.$type->elementName.'"]';
    } else {
      $this->_buffer .= '[local-name() = "'.$type->elementName.'"]';
    }
    return TRUE;
  }

  /**
  * Output the class selector to the buffer
  *
  * @param PhpCssAstSelectorSimpleId $class
  * @return boolean
  */
  public function visitSelectorSimpleId(PhpCssAstSelectorSimpleId $id) {
    $this->_buffer .= '[@id = "#'.$id->id.']';
    return TRUE;
  }


  /**
  * Output the class selector to the buffer
  *
  * @param PhpCssAstSelectorSimpleClass $class
  * @return boolean
  */
  public function visitSelectorSimpleClass(PhpCssAstSelectorSimpleClass $class) {
    $this->_buffer .= sprintf(
      '[contains(concat(" ", normalize-space(@class), " "), " %s ")]."',
      $class->className
    );
    return TRUE;
  }
}