<?php

namespace Suru\Docebo\DoceboApiWrapper\Models;

class CourseMaterial extends Model {

    /**
     * Properties which should be converted to booleans.
     *
     * @var array
     */
    protected $booleans = [
        'locked',
    ];

    /**
     * The type of learning object returned (folder, test, scorm, etc).
     *
     * @type string
     */
    protected $type;

    /**
     * ID of the learning object.
     *
     * @type int
     */
    protected $id_org;

    /**
     * The title of the learning object.
     *
     * @type string
     */
    protected $title;

    /**
     * The user status for this learning object.
     *
     * @type string (optional)
     */
    protected $status;

    /**
     * True of this learning object is locked (e.g. a prerequisite must be satisfied).
     *
     * @type boolean (optional)
     */
    protected $locked;

    /**
     * The id of the scorm chapter (if type is scorm).
     *
     * @type integer (optional)
     */
    protected $id_scormitem;

    /**
     * The id of the user for whom status is returned at the object level.
     *
     * @type int (optional)
     */
    protected $id_user;

    /**
     * If the type of a CourseMaterial is a folder it may contain child items.
     *
     * @var array
     */
    protected $children;

    /**
     * Assign a collection to the children property.
     *
     * @param array $children
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
    }

}
