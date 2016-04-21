<?php

namespace Suru\Docebo\DoceboApiWrapper\Models;

use Assert\Assertion;
use Suru\Docebo\DoceboApiWrapper\Enumerations\CourseStatus;
use Suru\Docebo\DoceboApiWrapper\Enumerations\CourseSubscribeMethod;
use Suru\Docebo\DoceboApiWrapper\Enumerations\Languages;

class Course extends Model {

    /**
     * Properties which should be converted to booleans.
     *
     * @var array
     */
    protected $booleans = [
        'selling',
    ];

    protected $dates = [
        'sub_start_date',
        'sub_end_date',
        'date_begin',
        'date_end',
    ];

    protected $id;
    protected $code;
    protected $name;
    protected $description;
    protected $language;
    protected $status;
    protected $selling;
    protected $price;
    protected $subscribe_method;
    protected $edition; // deprecated
    protected $type;
    protected $sub_start_date;
    protected $sub_end_date;
    protected $date_begin;
    protected $date_end;
    protected $link;
    protected $materials;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        foreach ($attributes as $key => $value)
        {
            if (strpos($key, 'course_') === 0)
            {
                $this->setProperty(substr($key, 7), $value);
            }
        }

        if (isset($this->status))
        {
            $this->status = CourseStatus::createFromValue((int) $this->status);
        }

        if (isset($this->subscribe_method))
        {
            $this->subscribe_method = CourseSubscribeMethod::createFromValue((int) $this->subscribe_method);
        }

        if (isset($this->language))
        {
            $language = (Languages::valueContainsLocale($this->language)) ?
                Languages::stripLocaleFromValue($this->language) :
                $this->language;

            $this->language = Languages::createFromValue($language);
        }
    }

    public function setMaterials($materials)
    {
        Assertion::isArray($materials);

        $this->materials = $materials;
    }

    public function hasMaterials()
    {
        if (is_null($this->materials))
        {
            return false;
        }

        return ! $this->materials->isEmpty();
    }

    /**
     * Returns the two character ISO 639-1 language code.
     *
     * @return string|null
     */
    public function getLanguageCodeAttribute()
    {
        return $this->language->getName();
    }

    public function getTypeAttribute()
    {
        $values = [
            'classroom' => 'Classroom',
            'elearning' => 'E Learning',
            'webinar' => 'Webinar',
        ];

        return $values[$this->type];
    }

    /**
     * Retrieves a count of course materials with the option to ignore folders
     * from the count but still count their contents.
     *
     * @param boolean $exclude_folder_types
     * @return int
     */
    public function getNumberOfMaterials($exclude_folder_types = true)
    {
        if (is_null($this->materials))
        {
            throw new Exception('Course materials have not yet been populated for Course #'.$this->id);
        }

        return $this->countMaterialsRecursive($this->materials, $exclude_folder_types);
    }

    /**
     * Recurses over course materials to determine how many are available. If
     * exclude folder types is set to true it does not count folder types.
     *
     * @param array $materials
     * @param boolean $exclude_folder_types
     * @return int
     */
    private function countMaterialsRecursive(array $materials, $exclude_folder_types = true)
    {
        // counter
        $count = count($materials);

        // filter out materials that are folders
        $folders = [];
        foreach ($materials as $material)
        {
            if ($material->type === 'folder')
            {
                $folders[] = $material;
            }
        }

        // subtract the number of folders if ignoring folders from count
        if ($exclude_folder_types)
        {
            $count -= count($folders);
        }

        // recurse through folders to find child items
        foreach ($folders as $folder)
        {
            $count += $this->countMaterialsRecursive($folder->children, $exclude_folder_types);
        }

        // return count for this directory level
        return $count;
    }
}
