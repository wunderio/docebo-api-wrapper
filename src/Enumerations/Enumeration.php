<?php  namespace Suru\Docebo\DoceboApiWrapper\Enumerations;

use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use Suru\Docebo\DoceboApiWrapper\Helpers\Helper;

/**
 * Abstract class as a base for Enumerations. Eg. ProjectStatus, Gender, ReportType, etc.
 *
 * TODO: Allow both integers and strings as valid values (need to change how checking works in this class).
 *
 * To create an enum class extend this class and simply define the options as constants and values. For example:
 *
 *    class Gender extends Enum {
 *        const MALE = "M";
 *        const FEMALE = "F";
 *    }
 *
 * You can then create instances of the Gender enum with in the following ways:
 *
 *     $male = new Gender("MALE");
 *     $male = Gender::createFromName("MALE");
 *     $male = Gender::createFromValue("M");
 *
 * It can also be used for bitfield-style enums such as ProjectStatus:
 *
 *     class ProjectStatus extends Enum {
 *         const DRAFT = 1;
 *         const PROPOSAL = 2;
 *         const ACTIVE = 3;
 *         const INACTIVE = 4;
 *         const COMPLETE = 5;
 *     }
 *
 * Note that the Enumeration class will consider the values 1, 1.0, "1" and "1.0" to be identical since some database
 * configurations return integers as strings. If you use numbers as your enumeration values stick to whole integers to
 * avoid ambiguity.
 *
 * To use with Eloquent models, define an accessor that converts the stored value in the database to the Enum object,
 * and a mutator that converts the given Enum object to its value to be stored. Eg.
 *
 *     class Project extends BaseModel {
 *         public function getStatusAttribute($value)
 *         {
 *             return ProjectStatus::createFromValue($value);
 *         }
 *
 *         public function setStatusAttribute(ProjectStatus $status)
 *         {
 *             $this->attributes['status'] = $status->getValue();
 *         }
 *     }
 */
abstract class Enumeration {

    /**
     * The value of this Enum object instance.
     *
     * @var string|integer
     */
    private $value;

    /**
     * Store existing constants in a static cache per object.
     *
     * @var array
     */
    protected static $cache = [];

    /**
     * Create a new Enum from the given name.
     *
     * @param  string $name
     * @return Enum
     */
    final public function __construct($name)
    {
        $this->value = self::getValueOf($name);
    }

    /**
     * Create a new Enum from the given name.
     *
     * @param  string $name
     * @return Enum
     */
    final public static function createFromName($name)
    {
        return new static($name);
    }

    /**
     * Create a new Enum from the given value.
     *
     * @param  string $value
     * @return Enum
     */
    final public static function createFromValue($value)
    {
        return new static(self::getNameOf($value));
    }

    /**
     * Return enum list.
     *
     * @return array
     */
    final public static function getOptions()
    {
        $class = get_called_class();

        if ( ! array_key_exists($class, static::$cache))
        {
            $refClass = new ReflectionClass($class);
            $constants = $refClass->getConstants();

            self::validateValues($constants);

            static::$cache[$class] = $constants;
        }

        return static::$cache[$class];
    }

    final private static function validateValues($constants)
    {
        $ambiguousNames = [];
        $helpers = new Helper;

        foreach ($constants as $name => $value)
        {
            // Check for any exact matches
            $matchingNames = array_keys($constants, $value, true);

            // Also check for integerish matches against constants (other than the one we're checking)
            foreach ($constants as $siblingName => $siblingValue)
            {
                if ($siblingName === $name) { continue;}

                if ($helpers->isIntegerish($value) &&
                    $helpers->isIntegerish($siblingValue) &&
                    $siblingValue == $value)
                {
                    $matchingNames[] = $siblingName;
                }
            }

            if (count($matchingNames) > 1)
            {
                $ambiguousNames[var_export($value, true)] = $matchingNames;
            }
        }

        if ( ! empty($ambiguousNames))
        {
            $message = 'All values in ' . get_called_class() . ' must be unique. The following are ambiguous: ';
            $message .= implode(', ', array_map(function ($names) use ($constants) {
                return implode('/', $names) . '=' . var_export($constants[$names[0]], true);
            }, $ambiguousNames));

            throw new LogicException($message);
        }
    }

    /**
     * Return enum values.
     *
     * @return array
     */
    final public static function getValues()
    {
        return array_values(self::getOptions());
    }

    /**
     * Return enum names.
     *
     * @return array
     */
    final public static function getNames()
    {
        return array_keys(self::getOptions());
    }

    /**
     * Whether the value is defined in the Enum options.
     *
     * @param  integer|string $value
     * @return boolean
     */
    final public static function valueIsDefined($candidate)
    {
        $helpers = new Helper;

        foreach (self::getOptions() as $name => $value)
        {
            // If the value defined in the enumeration is integerish, use loose comparison
            if ($helpers->isIntegerish($value) && $helpers->isIntegerish($candidate) && $value == $candidate) { return true; }

            // Otherwise check strictly
            if ($value === $candidate) { return true; }
        }

        return false;
    }

    /**
     * Whether the name is defined in the Enum options.
     *
     * @param  string $name
     * @return boolean
     */
    final public static function nameIsDefined($name)
    {
        return isset(self::getOptions()[$name]);
    }

    /**
     * Validate that the value exists in the Enum options.
     *
     * @param  integer|string $value
     * @throws InvalidArgumentException Thrown if the given value is not defined.
     */
    final protected static function validateValue($value)
    {
        if ( ! self::valueIsDefined($value))
        {
            throw new InvalidArgumentException(sprintf('value is not defined in %s: %s', get_called_class(), $value));
        }
    }

    /**
     * Validate that the name exists in the Enum options.
     *
     * @param  string $name
     * @throws InvalidArgumentException Thrown if the given name is not defined.
     */
    final protected static function validateName($name)
    {
        if ( ! self::nameIsDefined($name))
        {
            throw new InvalidArgumentException(sprintf('name is not defined in %s: %s', get_called_class(), $name));
        }
    }

    /**
     * Return the name of the given value.
     *
     * @param integer|string $value
     * @return string
     * @throws InvalidArgumentException Thrown if the given value is not defined.
     */
    final public static function getNameOf($value)
    {
        self::validateValue($value);

        return array_search($value, self::getOptions(), true);
    }

    /**
     * Return the value of the given name.
     *
     * @param string $name
     * @return integer|string
     * @throws InvalidArgumentException Thrown if the given name is not defined.
     */
    final public static function getValueOf($name)
    {
        self::validateName($name);

        return self::getOptions()[$name];
    }

    /**
     * Return the name of the Enum object.
     *
     * @return string
     */
    final public function getName()
    {
        return self::getNameOf($this->value);
    }

    /**
     * Return the value of the Enum object.
     *
     * @return integer|string
     */
    final public function getValue()
    {
        return $this->value;
    }

    /**
     * Whether this Enum is equal to the given Enum, both in class and value.
     *
     * @param  Enum  $other
     * @return boolean
     */
    final public function equals(Enumeration $other)
    {
        if (get_class($other) !== get_class($this))
        {
            throw new InvalidArgumentException(sprintf('%s::equals can only check against other instances of %s but an instance of %s was given.', get_class($this), get_class($this), get_class($other)));
        }

        return $this->isSameValueAs($other->getValue());
    }

    /**
     * Whether this Enum has the same value as the given value.
     *
     * @param  integer|string $value
     * @return boolean
     */
    final public function isSameValueAs($value)
    {
        // If the value is integerish, use loose comparison
        if ((new Helper)->isIntegerish($value))
        {
            return $this->getValue() == $value;
        }

        // Otherwise check strictly
        return $this->getValue() === $value;
    }

    /**
     * Return the ordinal of this Enum.
     *
     * @return integer
     */
    final public function getOrdinal()
    {
        return self::getOrdinalOfValue($this->value);
    }

    /**
     * Return the ordinal of the given name.
     *
     * @param  string $name
     * @return integer
     */
    final public static function getOrdinalOfName($name)
    {
        self::validateName($name);

        return array_search($name, self::getNames(), true);
    }

    /**
     * Return the ordinal of the given value.
     *
     * @param  integer|string $value
     * @return integer
     */
    final public static function getOrdinalOfValue($value)
    {
        self::validateValue($value);

        return array_search($value, self::getValues(), true);
    }

    /**
     * Compare this Enum's ordinal to that of another Enum of the same type.
     *
     * @param  Enum $other
     * @return integer -1, 0, 1
     */
    final public function compareTo(Enumeration $other)
    {
        if (get_class($other) !== get_class($this))
        {
            throw new InvalidArgumentException(sprintf('%s::compareTo can only compare against other instances of %s but an instance of %s was given.', get_class($this), get_class($this), get_class($other)));
        }

        $thisOrdinal  = $this->getOrdinal();
        $otherOrdinal = $other->getOrdinal();

        if ($thisOrdinal === $otherOrdinal)
        {
            return 0;
        }

        return $thisOrdinal > $otherOrdinal ? 1 : -1;
    }

    /**
     * Return the string value of this Enum (its option name).
     *
     * @return string
     */
    final public function toString()
    {
        return self::getNameOf($this->value);
    }

    /**
     * Cast this enum to a string.
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->toString();
    }

}