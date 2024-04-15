<?php
/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: zip.php                                              *
 *        Copyright: (C) 2002-2023 4homepages.de                          *
 *            Email: 4images@4homepages.de                                * 
 *              Web: http://www.4homepages.de                             * 
 *    Scriptversion: 1.10                                                 *
 *                                                                        *
 **************************************************************************
 *                                                                        *
 *    Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-       *
 *    bedingungen (Lizenz.txt) für weitere Informationen.                 *
 *    ---------------------------------------------------------------     *
 *    This script is NOT freeware! Please read the Copyright Notice       *
 *    (Licence.txt) for further information.                              *
 *                                                                        *
 *************************************************************************/
if (!defined('ROOT_PATH')) {
  die("Security violation");
}

/*
On-the-Fly Zip-File creation

Based on classes by:

Eric Mueller
http://www.themepark.com

Denis O.Philippov
http://www.atlant.ru

testnutzer123
http://www.4homepages.de/forum/index.php?action=profile;u=11533
*/

class Zipfile {

  var $tmpfp;
  var $ctrl_dirs = array(); // central directory
  var $offset = 0;
  var $level = 9;

  function __construct($level = 9) {
    $this->level = $level;

    // get and check our temp file
    // remark: when closed file should "vanish".
    // on the other hand I don't got a destructor in php4
    // but php4 should auto-close all open files when finalizing script.
    $this->tmpfp = @tmpfile();
    if (!$this->tmpfp) {
      die("Cannot get temporary file!");
    }

    @register_shutdown_function(array(&$this, 'close'));
  }

  function close() {
    if ($this->tmpfp) {
      @fclose($this->tmpfp);
    }
  }

  function add_file($data, $name) {
    // calculate crc32
    $crc = crc32($data);

    // get data-length
    $len = strlen($data);

    // actual compression
    $zdata = gzcompress($data, $this->level);

    // not needed any longer
    unset($data);

    // fixing
    $zdata = substr($zdata, 2, strlen($zdata) - 6); // fix crc bug

    // compressed length
    $zlen = strlen($zdata);

    // header for localfile
    fwrite($this->tmpfp, "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00\x00\x00\x00\x00");

    // additional header values
    fwrite($this->tmpfp, pack('V', $crc));
    fwrite($this->tmpfp, pack('V', $zlen));
    fwrite($this->tmpfp, pack('V', $len));
    fwrite($this->tmpfp, pack('v', strlen($name)));
    fwrite($this->tmpfp, pack('v', 0));
    fwrite($this->tmpfp, $name);

    // data
    fwrite($this->tmpfp, $zdata);
    //not needed any longer
    unset($zdata);

    // datasegment (this is optional FWIK)
    fwrite($this->tmpfp, pack('V', $crc));
    fwrite($this->tmpfp, pack('V', $zlen));
    fwrite($this->tmpfp, pack('V', $len));

    // add to our TOC
    $this->ctrl_dirs[] = array(
        'crc' => $crc,
        'zlen' => $zlen,
        'len' => $len,
        'name' => $name,
        'offset' => $this->offset
    );

    // change offset
    $this->offset = ftell($this->tmpfp);
  }

  function prepare() {
    // empty file!?
    if (!sizeof($this->ctrl_dirs))
    {
        die("Zipfile is empty!");
    }

    // offset means: length of the data-segments
    $offset = ftell($this->tmpfp);

    // building the TOC
    $ctrlDir = '';
    for ($t = 0; $t < sizeof($this->ctrl_dirs); $t++)
    {
        $dir = $this->ctrl_dirs[$t];

        // Header
        $ctrlDir .= "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00\x00\x00\x00\x00";

        // additional header stuff
        $ctrlDir .= pack('V', $dir['crc']);
        $ctrlDir .= pack('V', $dir['zlen']);
        $ctrlDir .= pack('V', $dir['len']);
        $ctrlDir .= pack('v', strlen($dir['name']));
        $ctrlDir .= pack('v', 0);
        $ctrlDir .= pack('v', 0);
        $ctrlDir .= pack('v', 0);
        $ctrlDir .= pack('v', 0);
        $ctrlDir .= pack('V', 32);
        $ctrlDir .= pack('V', $dir['offset']);
        $ctrlDir .= $dir['name'];
    }

    // TOC length
    $ctrlDirLen = strlen($ctrlDir);

    // calculating size of whole file
    $len = $offset + $ctrlDirLen + 22; //8 + 2 + 2 + 4 + 4 + 2;

    // put out dir-entries
    //echo $ctrlDir;
    fwrite($this->tmpfp, $ctrlDir);
    // no longer needed
    unset($ctrlDir);

    // finalise the TOC
    //echo "\x50\x4b\x05\x06\x00\x00\x00\x00";
    fwrite($this->tmpfp, "\x50\x4b\x05\x06\x00\x00\x00\x00");

    // put out "files on disk" and "files whole"
    //echo pack('v', sizeof($this->ctrl_dirs));
    //echo pack('v', sizeof($this->ctrl_dirs));
    fwrite($this->tmpfp, pack('v', sizeof($this->ctrl_dirs)));
    fwrite($this->tmpfp, pack('v', sizeof($this->ctrl_dirs)));

    // put out length of TOC
    //echo pack('V', $ctrlDirLen);
    fwrite($this->tmpfp, pack('V', $ctrlDirLen));

    // put out start of TOC
    //echo pack('V', $offset);
    fwrite($this->tmpfp, pack('V', $offset));

    // used for archive comments
    // we dont use any -> tell the application com-length is null
    //echo "\x00\x00";
    fwrite($this->tmpfp, "\x00\x00");

    return $len;
  }

  function send($file_name) {
    $len = $this->prepare();

    // this is required because otherwise our content-length in conjunction with content-encoding: gzip
    // would cause archive corruption
    @ini_set('zlib.output_compression', '0');
    @ini_set('output_handler', NULL);

    // should work for all browsers (at least for the major three: moz, opera, ie)
    header('Content-Type: application/octet-stream');

    // will at least keep some download-managers away from sending more than one request
    header('Accept-Ranges: none');

    // this bit will tell the browser to
    // a) save the file
    // b) the default name
    header(sprintf('Content-Disposition: attachment; filename="%s"', $file_name));

    // if we have compression/op_handler active we would cause archive corruption when sending content length
    // thus we simply don't do it in this case (s.a.)
    if (!@ini_get('zlib.output_compression') && !@ini_get('output_handler')) {
        header(sprintf('Content-Length: %d', $len));
    }

    // pass thru the data
    fseek($this->tmpfp, 0);
    fpassthru($this->tmpfp);
    $this->close();
  }

  function store($file_name) {
    $len = $this->prepare();

    $fp = fopen($file_name, "wb");

    if (!$fp) {
        return false;
    }

    fseek($this->tmpfp, 0);

    while (!feof($this->tmpfp)) {
      fwrite($fp, fread($this->tmpfp, 8192));
    }

    fclose($fp);

    $this->close();
  }
} // end of class
?>
