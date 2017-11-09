<?php
/**
*
*
*/

//namespace follows directory pattern
namespace core\libs\base;

defined('ACCESS') || AppError::exitApp();

class Uploader extends \FuniObject
{

    protected $_file;

    protected $_maxUploadSize;

    protected $_mimeTypes = array(

                            # Text
                            'text' => array(
                                                'txt' => 'text/plain',
                                                'html' => 'text/html',
                                                'js' => 'text/javascript',
                                                'css' => 'text/css'
                                            ),
                            # images
                            'image' => array(
                                                'jpg' => 'image/jpeg',
                                                'png' => 'image/png',
                                                'gif' => 'image/gif'
                                            ),
                            # Documents
                            'document' => array(
                                                    'pdf' => 'application/pdf',
                                                    'doc' => 'application/msword',
                                                    // 'docx' => 'application/msword',
                                                    'xls' => '',
                                                    'xlsx' => 'application/vnd.ms-excel'
                                                ),
                            # Videos
                            'video' => array(
                                                'mov' => 'video/mov',
                                                'avi' => 'video/avi',
                                                'wmv' => 'video/wmv',
                                                'mp4' => 'video/mpeg4',
                                                'mp4' => 'video/mp4',
                                                'ogg' => 'video/ogg',
                                                'ogv' => 'video/ogv',
                                                'webm' => 'video/webm'
                                            ),
                            # Audios
                            'audio' => array(
                                                'mp3' => 'audio/mpeg3',
                                                'ogg' => 'audio/ogg',
                                                'wav' => 'audio/wav'
                                            )

                        );

    protected $_allowedUploadTypes = array();

    function __construct(argument)
    {
        # code...
    }

    public function setMaxUploadSize($value)
    {
        # code...
        $this->_maxUploadSize = $value;
        return $this;
    }

    public function setUploadAllowedTypes(Array $allowedTypes)
    {
        # loop tru allowed types
        foreach ($allowedTypes as $key) {

            # check if key is a group in MimeTypes
            if (array_key_exists(strtolower($key), $this->_mimeTypes)) {

                # append mimeTypes in this group to allowed mimeTypes
                $this->_allowedUploadTypes[] = $this->_mimeTypes[$key];
            }
        }

        return $this;
    }

    public function upload()
    {
        # to be implemented by descendants
    }
}


?>
