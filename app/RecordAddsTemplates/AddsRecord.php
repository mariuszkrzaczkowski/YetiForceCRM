<?php
/**
 * Base record adds template file.
 *
 * @package   App
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Arkadiusz Sołek <a.solek@yetiforce.com>
 */

namespace App\RecordAddsTemplates;

use App\Request;

/**
 * Base record adds template class.
 *
 * @internal
 * @coversNothing
 */
final class AddsRecord
{
	/** Icon. @var string */
	public $icon = 'mdi mdi-expand-all';
	/** Label. @var string */
	public $label = 'Mass creation';
	/** Name. @var string */
	public $name = 'AddsRecord';
	/** Record structure. @var array */
	public $recordStructure;
	/** Fields mapping for loading record data.  @var array */
	public $modulesFieldsMap = [
		'Accounts' => [
			['type' => 'field', 'fieldName' => 'legal_form', 'defaultValue' => '', 'mandatory' => true],
			['type' => 'field', 'fieldName' => 'accountname', 'defaultValue' => '', 'mandatory' => true],
			['type' => 'field', 'fieldName' => 'vat_id', 'defaultValue' => '', 'mandatory' => false],
			['type' => 'field', 'fieldName' => 'registration_number_2', 'defaultValue' => '', 'mandatory' => false],
			['type' => 'field', 'fieldName' => 'registration_number_1', 'defaultValue' => '', 'mandatory' => false]
		],
		'Contacts' => [
			['type' => 'field', 'fieldName' => 'lastname', 'defaultValue' => '', 'mandatory' => true],
			['type' => 'field', 'fieldName' => 'firstname', 'defaultValue' => '', 'mandatory' => false],
			['type' => 'field', 'fieldName' => 'contactstatus', 'defaultValue' => '', 'mandatory' => false],
			['type' => 'field', 'fieldName' => 'phone', 'defaultValue' => '', 'mandatory' => false],
			['type' => 'field', 'fieldName' => 'email', 'defaultValue' => '', 'mandatory' => false]
		],
		'SSalesProcesses' => [
			['type' => 'field', 'fieldName' => 'subject', 'defaultValue' => '', 'mandatory' => true],
			['type' => 'field', 'fieldName' => 'estimated_date', 'defaultValue' => '', 'mandatory' => true],
			['type' => 'field', 'fieldName' => 'estimated', 'defaultValue' => '', 'mandatory' => true],
			['type' => 'field', 'fieldName' => 'ssalesprocesses_type', 'defaultValue' => '', 'mandatory' => true],
		]
	];

	/**
	 * Check permission to method.
	 *
	 * @throws \Api\Core\Exception
	 */
	public function checkPermission()
	{
		foreach (array_keys($this->modulesFieldsMap) as $moduleName) {
			$recordModel = \Vtiger_Record_Model::getCleanInstance($moduleName);
			if (!$recordModel->isCreateable()) {
				throw new \App\Exceptions\NoPermittedToRecord('ERR_NO_PERMISSIONS_FOR_THE_RECORD', 406);
			}
		}
	}

	/**
	 * Function to get the record model based on the request parameters.
	 *
	 * @param \App\Request $request
	 *
	 * @return \Vtiger_Record_Model
	 */
	public function getRecordModelFromRequest(Request $request)
	{
		$recordModel = \Vtiger_Record_Model::getCleanInstance($request->getModule());
		$fieldModelList = $recordModel->getModule()->getFields();
		foreach ($this->modulesFieldsMap[$recordModel->getModuleName()] as $value) {
			if ('virtual' === $value['type']) {
				continue;
			}
			unset($value['type']);
			$fieldModel = $fieldModelList[$value['fieldName']];
			if (!$fieldModel->isWritable()) {
				continue;
			}
			if ($request->has($value['fieldName'])) {
				$fieldModel->getUITypeModel()->setValueFromRequest($request, $recordModel);
			}
		}
		return $recordModel;
	}

	/**
	 * Record validation before saving.
	 *
	 * @param \Vtiger_Record_Model $recordModel
	 *
	 * @return array
	 */
	public function preSaveValidation($recordModel)
	{
		$eventHandler = $recordModel->getEventHandler();
		$result = [];
		foreach ($eventHandler->getHandlers(\App\EventHandler::EDIT_VIEW_PRE_SAVE) as $handler) {
			if (!(($response = $eventHandler->triggerHandler($handler))['result'] ?? null)) {
				$result[] = $response;
			}
		}
		return ['preSave' => $result];
	}

	/**
	 * Checks if the value of the field is not empty.
	 *
	 * @param \App\Request $request
	 *
	 * @return bool
	 */
	// public function validationFields(Request $request)
	// {
	// 	$rawValues = $request->getAllRaw();
	// 	foreach ($this->modulesFieldsMap[$request->get('module')] as $value) {
	// 		if (!empty($value['mandatory'])) {
	// 			if ('' === $rawValues[$value['fieldName']]) {
	// 				throw new \App\Exceptions\AppException("Nie wprowadzono obowiązkowej wartości dla pola: {$value['fieldName']}", 403);
	// 			}
	// 		}
	// 	}
	// 	return true;
	// }

	/**
	 * Save records.
	 *
	 * @param \App\Request $request
	 *
	 * @return array
	 */
	public function save(Request $request)
	{
		$result = [];
		$requestAccounts = new Request($request->getRaw('Accounts'), false);
		// $validationAccounts = $this->validationFields($requestAccounts);
		// if ($validationAccounts) {
		$recordModelAccounts = $this->getRecordModelFromRequest($requestAccounts);
		$result = $this->preSaveValidation($recordModelAccounts);
		if ($result['preSave']) {
			return $result;
		}
		// }

		$requestContacts = new Request($request->getRaw('Contacts'), false);
		// $validationContacts = $this->validationFields($requestContacts);
		// if ($validationContacts) {
		$recordModelContacts = $this->getRecordModelFromRequest($requestContacts);
		$result = $this->preSaveValidation($recordModelContacts);
		if ($result['preSave']) {
			return $result;
		}
		// }

		$requestSSalesProcesses = new Request($request->getRaw('SSalesProcesses'), false);
		// $validationSSalesProcesses = $this->validationFields($requestSSalesProcesses);
		// if ($validationSSalesProcesses) {
		$recordModelSSalesProcesses = $this->getRecordModelFromRequest($requestSSalesProcesses);
		$result = $this->preSaveValidation($recordModelSSalesProcesses);
		if ($result['preSave']) {
			return $result;
		}
		// }

		$result = ['success' => true, 'message' => 'Rekordy zostały dodane.'];

		// if ($validationAccounts) {
		$recordModelAccounts->save();
		$accountsId = $recordModelAccounts->getId();
		// }

		// if ($validationContacts) {
		$recordModelContacts->set('parent_id', $accountsId);
		$recordModelContacts->save();
		$contactsId = $recordModelContacts->getId();
		// }

		// if ($validationSSalesProcesses) {
		$recordModelSSalesProcesses->set('related_to', $accountsId);
		$recordModelSSalesProcesses->save();
		$salesProcessesId = $recordModelSSalesProcesses->getId();
		// }

		if (!$accountsId || !$contactsId || !$salesProcessesId) {
			$result['success'] = false;
			$result['message'] = 'Nie wszystkie rekordy zostały dodane.';
		}
		return $result;
	}

	/**
	 * Get list blocks to form.
	 *
	 * @return array
	 */
	public function getBlocks()
	{
		$blockList = [];
		foreach ($this->recordStructure as $moduleName => $fields) {
			$blockModel = new \Vtiger_Block_Model();
			$blockModel->set('fields', $fields);
			$blockModel->set('icon', "yfm yfm-$moduleName");
			$blockList[$moduleName] = $blockModel;
		}
		return $blockList;
	}

	/**
	 * The fields that make up the record structure.
	 *
	 * @return array
	 */
	public function getFields()
	{
		$fields = [];
		foreach ($this->modulesFieldsMap as $moduleName => $values) {
			$moduleModel = \Vtiger_Module_Model::getInstance($moduleName);
			foreach ($values as $fieldParams) {
				if ('virtual' === $fieldParams['type']) {
					unset($fieldParams['type']);
					$fieldName = $fieldParams['params']['fieldName'];
					$fieldModel = \Vtiger_Field_Model::init($moduleName, $fieldParams['params'], $fieldParams['params']['fieldName']);
				} else {
					unset($fieldParams['type']);
					$fieldName = $fieldParams['fieldName'];
					$fieldModel = $moduleModel->getFieldByName($fieldName);
					if (!empty($fieldParams['defaultValue'])) {
						$fieldModel->set('fieldvalue', $fieldParams['defaultValue']);
					}
					// if (!empty($fieldParams['mandatory'])) {
					// 	$fieldModel->set('typeofdata', str_replace('M', 'O', $fieldModel->typeofdata));
					// }
				}
				$fields[$moduleName][$moduleName][$fieldName] = $fieldModel;
			}
		}
		if (empty($this->recordStructure)) {
			$this->recordStructure = $fields;
		}
		return $fields;
	}
}
