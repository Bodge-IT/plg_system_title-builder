<?php
/**
 * @copyright	(C) 2008 - 2014 Phil Walton. All rights reserved.
 * @license     GNU GPL <http://www.gnu.org/licenses/gpl.html>
 * @author		Phil Walton <phil@softforge.co.uk>
 * @link        http://www.softforge.co.uk
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentTitleandvariables extends JPlugin
{
	protected static $modules = array();
	protected static $mods = array();

	/**
	 * Plugin that loads module positions within content
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  mixed   true if there is an error. Void otherwise.
	 *
	 * @since   1.6
	 */
	public function onContentPrepare($context, &$article, $page = 0)	{		
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer')	{
			return true;
		}

		$app = JFactory::getApplication();		
		$user = JFactory::getUser();
		$params   = $this->params;
		$document = JFactory::getDocument();
		$menu     = $app->getMenu();
		$param1 = "";
		$param2 = "";
		$param3 = "";
				
		$param1 = $params->get('param1');
		$param2 = $params->get('param2');
		$param3 = $params->get('param3');
		
		$cap = $params->get('cap');	
		$fw = $params->get('fw');	
		$lw = $params->get('lw');
		function trunc($phrase, $max_words) {	
   		$phrase_array = explode(' ',$phrase);
   		$offset = 0;
         if(count($phrase_array) > $max_words && $max_words > 0){
     			$phrase = implode(' ',array_slice($phrase_array, $offset, $max_words));
     		}
   		return $phrase;
		}
		function trunclw($phrase, $max_words) {	
   		$phrase_array = explode(' ',$phrase);
   		$offset = count($phrase_array) - $max_words;   	
   		if(count($phrase_array) > $max_words && $max_words > 0){
     			$phrase = implode(' ',array_slice($phrase_array, $offset, $max_words));
     		}
   		return $phrase;
		}
		$pattern = "#\{titleandvariables\b(.*?)}#";
		$replacement = $document->title;
		preg_match_all($pattern, $article->text, $matches);
		$var = $matches[0];
		$count = 0;
		foreach ($var as $string) {
			$replacement = $document->title;
			$delimiter = "|";
			$elements =  explode($delimiter, $string);
			foreach($elements as $key=> $value)	{
				if (strpos($value,'fw') !== false) {
					$length_of_word = filter_var($elements[$key], FILTER_SANITIZE_NUMBER_INT);
  		     		$replacement = trunc($document->title, $length_of_word);
				}
				if (strpos($value,'lw') !== false) {
					$length_of_word = filter_var($elements[$key], FILTER_SANITIZE_NUMBER_INT);
  		     		$replacement = trunclw($document->title, $length_of_word);
				}
			}		
			foreach ($elements as $element) {
				switch ($element) {		    	
		  		case strpos($element,'lw'):
		    	break;	
		  		case strpos($element,'uf'):
		  			$patterns = "#\{titleandvariables\b(.*?)}#";
					$replace = ucfirst($replacement);
					$article->text = preg_replace($patterns, $replace, $article->text,1);		    		
		   	break;
		   	
		   	case strpos($element,'uw'):
		  			$patterns = "#\{titleandvariables\b(.*?)}#";		  			
					$replace = ucwords($replacement);
					$article->text = preg_replace($patterns, $replace, $article->text,1);		    		
		   	break;
		   	
		   	case strpos($element,'uc'):
		  			$patterns = "#\{titleandvariables\b(.*?)}#";
					$replace = strtoupper($replacement);	
					$article->text = preg_replace($patterns, $replace, $article->text,1);		    		
		   	break;		 
		   	  	
		   	case strpos($element,'lc'):
		  			$patterns = "#\{titleandvariables\b(.*?)}#";
					$replace = strtolower($replacement);
					$article->text = preg_replace($patterns, $replace, $article->text,1);		    		
		   	break;	   	

		  		default:
				}
			}
			$count=$count+1;
		}
	}
}