/* Log Item Filters 1.0.5 */
(function($){$(document).ready(function(){if(!_canLoad()){return}
_debugLog('Log Filter Script',aalLog);$('.copy-to-clipboard').click(function(event){var _oLogClone=$('.log').clone();_oLogClone.find('p:empty, dif:empty').remove();_oLogClone.find('.log-item, .log-item-title, .log-item-message').append('\n');var _bCopied=aalCopyToClipboard(_oLogClone[0]);alert(_bCopied?aalLog.labels.copied:aalLog.labels.not_copied)});var _oLogRaw=$('.log').clone();$('input.filter-include, input.filter-exclude').change(function(event){_filterLog(_oLogRaw.clone(),$('input.filter-include'),$('input.filter-exclude'))}).trigger('change');$(window).trigger('resize');var observer=new MutationObserver(_fixFormLayout);var target=document.querySelector('#side-sortables');observer.observe(target,{attributes:!0})});function _filterLog(oLogClone,oInputInclude,oInputExclude){var _sTypedInclude=oInputInclude.val();var _sTypedExclude=oInputExclude.val();var _aIncludes=_sTypedInclude.split(/,\s{0,}/).filter(item=>item);var _aExcludes=_sTypedExclude.split(/,\s{0,}/).filter(item=>item);_debugLog('filter include:',_aIncludes,'exclude: ',_aExcludes);oLogClone.find('.log-item').each(function(){var _sEntry=$(this).text();if(''!==_sTypedExclude&&_hasMatch(_sEntry,_aExcludes)){$(this).remove();return!0}
if(''!==_sTypedInclude&&!_hasMatch(_sEntry,_aIncludes)){$(this).remove();return!0}});$('.log').replaceWith(oLogClone.show());_fixFormLayout();$('.log-item-head').click(function(){$(this).closest('.log').find('.stack-trace').css({'min-height':'320px','width':'100%',});$(this).next().slideToggle('slow');return!1}).next().hide()}
$(window).resize(debounce(_fixFormLayout));function _fixFormLayout(event){var _iSectionWidth=$('.amazon-auto-links-section').width();if(!_iSectionWidth){return}
$('.log').width(_iSectionWidth).show();$('.debug-output').width(_iSectionWidth)}
$(document).on('keydown','input.filter-include, input.filter-exclude',function(event){if("Enter"!==event.key){return!0}
$(this).trigger('change');return!1});function _debugLog(...args){if(!parseInt(aalLog.debugMode)){return}
console.log(aalLog.pluginName,...args)}
function _canLoad(){if('undefined'===typeof aalLog){console.log('the log filter script is not loaded.');return!1}
if('undefined'===typeof aalCopyToClipboard){console.log('the utility script is not loaded.');return!1}
return!0}
function _hasMatch(sEntry,aItems){var _bFound=!1;$.each(aItems,function(index,_sItem){if(''===_sItem){return!0}
if(-1===sEntry.indexOf(_sItem)){return!0}
_bFound=!0;return!1});return _bFound}}(jQuery))