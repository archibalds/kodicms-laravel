<?php namespace KodiCMS\Datasource\Fields\Relation;

use KodiCMS\Datasource\Fields\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Builder;
use KodiCMS\Datasource\Contracts\SectionInterface;
use KodiCMS\Datasource\Contracts\DocumentInterface;
use KodiCMS\Datasource\Contracts\FieldTypeRelationInterface;
use KodiCMS\Datasource\Contracts\FieldTypeOnlySystemInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToRelation;

class BelongsTo extends Relation implements FieldTypeRelationInterface, FieldTypeOnlySystemInterface
{
	/**
	 * @var bool
	 */
	protected $hasDatabaseColumn = false;

	/**
	 * @param DocumentInterface $document
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function onGetHeadlineValue(DocumentInterface $document, $value)
	{
		return !is_null($relatedDocument = $document->getAttribute($this->getRelationName()))
			? \HTML::link($relatedDocument->getEditLink(), $relatedDocument->getTitle(), ['class' => 'popup'])
			: null;
	}

	/**
	 * @param Builder $query
	 * @param DocumentInterface $document
	 */
	public function querySelectColumn(Builder $query, DocumentInterface $document)
	{
		$query->with($this->getRelationName());
	}

	/**
	 * @param DocumentInterface $document
	 * @param SectionInterface $relatedSection
	 * @return BelongsToRelation
	 */
	public function getDocumentRelation(DocumentInterface $document, SectionInterface $relatedSection)
	{
		$instance = $relatedSection->getEmptyDocument()->newQuery();

		$foreignKey = $this->getSection()->getDocumentPrimaryKey();
		$otherKey =  str_replace('_belongs_to', '', $this->getDBKey());
		$relation = $this->getRelationName();

		return new BelongsToRelation($instance, $document, $foreignKey, $otherKey, $relation);
	}
}