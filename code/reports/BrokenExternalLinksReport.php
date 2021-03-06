<?php

/**
 * Content side-report listing items with external broken links
 * @package externallinks
 * @subpackage content
 */

class BrokenExternalLinksReport extends SS_Report {

	/**
	 * Returns the report title
	 *
	 * @return string
	 */
	public function title() {
		return _t('ExternalBrokenLinksReport.EXTERNALBROKENLINKS', "External broken links report");
	}

	public function columns() {
		return array(
			"Created" => "Checked",
			'Link' => array(
				'title' => 'External Link',
				'formatting' => function($value, $item) {
					return sprintf(
						'<a target="_blank" href="%s">%s</a>',
						Convert::raw2att($item->Link),
						Convert::raw2xml($item->Link)
					);
				}
			),
			'HTTPCodeDescription' => 'HTTP Error Code',
			"Title" => array(
				"title" => 'Item link is on',
				'formatting' => function($value, $item) {
					$item = $item->Item();
					return sprintf(
						'<a href="%s">%s</a>',
						Convert::raw2att($item->CMSEditLink()),
						Convert::raw2xml($item->Title)
					);
				}
			),
            'ClassChecked' => 'Class',
            'FieldChecked' => 'Field',
		);
	}

	/**
	 * Alias of columns(), to support the export to csv action
	 * in {@link GridFieldExportButton} generateExportFileData method.
	 * @return array
	 */
	public function getColumns() {
		return $this->columns();
	}

	public function sourceRecords() {
		$track = BrokenExternalItemTrackStatus::get_latest();
		if ($track) return $track->BrokenLinks();
		return new ArrayList();
	}

	public function getCMSFields() {
		Requirements::javascript('silverstripe-externallinks/javascript/BrokenExternalLinksReport.js');
		$fields = parent::getCMSFields();

		$reportResultSpan = '</ br></ br><h3 id="ReportHolder"></h3>';
		$reportResult = new LiteralField('ResultTitle', $reportResultSpan);
		$fields->push($reportResult);

		$button = '<button id="externalLinksReport" type="button">%s</button>';
		$runReportButton = new LiteralField(
			'runReport',
			sprintf(
				$button,
				_t('ExternalBrokenLinksReport.RUNREPORT', 'Create new report')
			)
		);
		$fields->push($runReportButton);

		return $fields;
	}
}
