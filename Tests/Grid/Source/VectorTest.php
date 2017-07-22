<?php

namespace APY\DataGridBundle\Grid\Tests\Source;

use APY\DataGridBundle\Grid\Column\Column;
use APY\DataGridBundle\Grid\Column\UntypedColumn;
use APY\DataGridBundle\Grid\Source\Vector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class VectorTest extends TestCase
{
    /**
     * @var Vector
     */
    private $vector;

    public function testCreateVectorWithEmptyData()
    {
        $this->assertAttributeEmpty('data', $this->vector);
    }

    public function testRaiseExceptionDuringVectorCreationWhenDataIsNotAVector()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Vector(['notAnArray'], []);
    }

    public function testRaiseExceptionDuringVectorCreationWhenEmptyVector()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Vector([[]], []);
    }

    public function testCreateVectorWithColumns()
    {
        $column = $this->createMock(Column::class);
        $column2 = $this->createMock(Column::class);
        $columns = [$column, $column2];

        $vector = new Vector([], $columns);

        $this->assertAttributeEquals($columns, 'columns', $vector);
    }

    public function testInitialiseWithoutData()
    {
        $this->vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEmpty('columns', $this->vector);
    }

    public function testInizialiseWithGuessedColumnsMergedToAlreadySettedColumns()
    {
        $columnId = 'cId';
        $column = $this->createMock(Column::class);
        $column
            ->method('getId')
            ->willReturn($columnId);

        $column2Id = 'c2Id';
        $column2 = $this->createMock(Column::class);
        $column2
            ->method('getId')
            ->willReturn($column2Id);

        $vector = new Vector([['c3Id' => 'c3', 'c4Id' => 'c4']], [$column, $column2]);

        $uc1 = new UntypedColumn([
            'id'         => 'c3Id',
            'title'      => 'c3Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c3Id',
        ]);
        $uc1->setType('text');

        $uc2 = new UntypedColumn([
            'id'         => 'c4Id',
            'title'      => 'c4Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c4Id',
        ]);
        $uc2->setType('text');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$column, $column2, $uc1, $uc2], 'columns', $vector);
    }

    public function testInizialiseWithoutGuessedColumns()
    {
        $columnId = 'cId';
        $column = $this->createMock(Column::class);
        $column
            ->method('getId')
            ->willReturn($columnId);

        $column2Id = 'c2Id';
        $column2 = $this->createMock(Column::class);
        $column2
            ->method('getId')
            ->willReturn($column2Id);

        $vector = new Vector([[$columnId => 'c1', $column2Id => 'c2']], [$column, $column2]);

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$column, $column2], 'columns', $vector);
    }

    public function testInizialiseWithEmptyValueGuessedColumn()
    {
        $vector = new Vector([['c1Id' => '']]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('text');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithNullValueGuessedColumn()
    {
        $vector = new Vector([['c1Id' => null]]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('text');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithArrayValueGuessedColumn()
    {
        $vector = new Vector([['c1Id' => []]]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('array');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithDateTimeValueGuessedColumn()
    {
        $vector = new Vector([['c1Id' => new \DateTime()]]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('datetime');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithStringButNotDateValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => 'thisIsAString']]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('text');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithDateStringValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => '2017-07-22']]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('date');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithDatetimeStringValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => '2017-07-22 12:00:00']]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('datetime');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithTrueValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => true]]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('boolean');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithFalseValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => true]]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('boolean');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithTrueIntValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => 1]]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('boolean');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithFalseIntValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => 0]]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('boolean');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithTrueStringValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => '1']]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('boolean');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithFalseStringValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => '0']]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('boolean');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithNumberValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => 12]]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('number');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithBooleanAndNotNumberValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => true], ['c1Id' => '2017-07-22']]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('text');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithBooleanAndNumberValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => true], ['c1Id' => 20]]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('number');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithDateAndNotDatetimeValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => '2017-07-22'], ['c1Id' => 20]]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('text');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function testInizialiseWithDateAndDatetimeValueGuessedColumn()
    {
        $value =  new \DateTime();
        $value->setTime(0, 0, 0);

        $vector = new Vector([['c1Id' => '2017-07-22'], ['c1Id' => '2017-07-22 11:00:00']]);

        $uc = new UntypedColumn([
            'id'         => 'c1Id',
            'title'      => 'c1Id',
            'source'     => true,
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'field'      => 'c1Id',
        ]);
        $uc->setType('datetime');

        $vector->initialise($this->createMock(Container::class));

        $this->assertAttributeEquals([$uc], 'columns', $vector);
    }

    public function setUp()
    {
        $this->vector = new Vector([], []);
    }
}

class VectorObj
{
}