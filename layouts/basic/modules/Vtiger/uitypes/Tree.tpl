{*<!-- {[The file is published on the basis of YetiForce Public License 3.0 that can be found in the following directory: licenses/LicenseEN.txt or yetiforce.com]} -->*}
{strip}
	{assign var=FIELD_NAME value=$FIELD_MODEL->getName()}
	{assign var="FIELD_INFO" value=\App\Purifier::encodeHtml(\App\Json::encode($FIELD_MODEL->getFieldInfo()))}
	{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
	{assign var=FIELD_VALUE value=$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'),$RECORD)}
	{assign var=DISPLAY_VALUE value=$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'),false,$RECORD,true)}
	<input name="{$FIELD_MODEL->getFieldName()}" type="hidden" value="{$FIELD_VALUE}" class="sourceField" data-displayvalue='{$DISPLAY_VALUE}' data-fieldinfo='{$FIELD_INFO}' data-multiple="{if $FIELD_MODEL->getUIType() == 309 }1{else}0{/if}" data-treetemplate="{$FIELD_MODEL->getFieldParams()}">
	{assign var="displayId" value=$FIELD_MODEL->get('fieldvalue')}
	<div class="input-group">
		{if $FIELD_MODEL->get('displaytype') != 10}
			<span class="input-group-addon clearTreeSelection cursorPointer">
				<span id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_clear" class='fas fa-times-circle' title="{\App\Language::translate('LBL_CLEAR', $MODULE)}"></span>
			</span>
		{/if}
		<input id="{$FIELD_NAME}_display" name="{$FIELD_MODEL->getFieldName()}_display" type="text" class="{if (($VIEW eq 'Edit'))} col-md-7 {else} col-md-8 {/if}	marginLeftZero treeAutoComplete form-control" {if !empty($displayId)}readonly="true"{/if}
			   value="{$DISPLAY_VALUE}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
			   data-fieldinfo='{$FIELD_INFO}' {if $FIELD_MODEL->get('displaytype') != 10}placeholder="{\App\Language::translate('LBL_TYPE_SEARCH',$MODULE)}"{/if}
			   {if !empty($SPECIAL_VALIDATOR)}data-validator='{\App\Json::encode($SPECIAL_VALIDATOR)}'{/if} {if $FIELD_MODEL->isEditableReadOnly()}readonly="readonly"{/if}/>
		{if $FIELD_MODEL->get('displaytype') != 10}
			<span class="input-group-addon treePopup cursorPointer">
				<span id="{$MODULE}_editView_fieldName_{$FIELD_NAME}_select" class="fas fa-search" title="{\App\Language::translate('LBL_SELECT', $MODULE)}" ></span>
			</span>
		{/if}
	</div>
{/strip}
