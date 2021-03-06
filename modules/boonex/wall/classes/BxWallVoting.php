<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

bx_import('BxDolModule');
bx_import('BxTemplVotingView');

class BxWallVoting extends BxTemplVotingView
{
	var $_oModule;

    function BxWallVoting($sSystem, $iId, $iInit = 1)
    {
        parent::BxTemplVotingView($sSystem, $iId, $iInit);

        $this->_oModule = BxDolModule::getInstance('BxWallModule');
    }

    function getVotingTimeline($bCount = true)
    {
    	return $this->_getVotingElement(array(
    		'show_count' => $bCount
    	));
    }

	function getVotingOutline($bCount = true)
    {
    	return $this->_getVotingElement(array(
    		'template_do_vote' => 'outline_voting_do_vote.html',
    		'show_count' => $bCount
    	));
    }

	function isVotingAllowed($isPerformAction = false)
    {
        return $this->checkAction($isPerformAction);
    }

    protected function _getVotingElement($aParams = array())
    {
    	if(!$this->isVotingAllowed())
    		return '';

		$sTmplMain = !empty($aParams['template_main']) ? $aParams['template_main'] : 'voting.html';
    	$sTmplDoVote = !empty($aParams['template_do_vote']) ? $aParams['template_do_vote'] : 'voting_do_vote.html';
    	$bCount = isset($aParams['show_count']) ? (bool)$aParams['show_count'] : true;

    	$sName = $this->getSystemName();
    	$iObjId = $this->getId();

		$sHtmlId = $sName . '_like' . $iObjId;
		$sHtmlIdSlider = $sHtmlId . '_slider' . $iObjId;
		$sJsObject = $this->_oModule->_oConfig->getJsObject('voting') . $this->_toName($sHtmlId);

		$iMax = $this->getMaxVote();
		return $this->_oModule->_oTemplate->parseHtmlByName($sTmplMain, array(
    		'html_id' => $sHtmlId,
    		'html_id_slider' => $sHtmlIdSlider,
    		'js_object' => $sJsObject,
			'class' => 'wall-voting',
    		'name' => $sName,
    		'object_id' => $iObjId,
    		'size_x' => $this->_iSizeStarSmallX,
    		'max' => $iMax,
			'do_vote' => $this->_oModule->_oTemplate->parseHtmlByName($sTmplDoVote, array(
				'js_object' => $sJsObject,
				'max' => $iMax,
				'bx_if:show_count' => array(
	    			'condition' => $bCount,
	    			'content' => array(
	    				'count' => $this->getVoteCount()
	    			)
	    		)
			))
    	));
    }

    protected function _toName($s)
    {
		return str_replace(' ', '', ucwords(str_replace('_', ' ', $s)));
    }
}
