<?php
/**
 * Audio Player
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author Marcel Scherello <audioplayer@scherello.de>
 * @copyright 2016-2019 Marcel Scherello
 */

namespace OCA\audioplayer\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IL10N;
use OCP\IDbConnection;
use OCP\ITagManager;

/**
 * Controller class for Sidebar.
 */
class SidebarController extends Controller
{

    private $userId;
    private $db;
    private $l10n;
    private $tagger;
    private $tagManager;
    private $DBController;

    public function __construct(
        $appName,
        IRequest $request,
        $userId,
        IL10N $l10n,
        ITagManager $tagManager,
        IDBConnection $db,
        DbController $DBController
    )
    {
        parent::__construct($appName, $request);
        $this->appName = $appName;
        $this->l10n = $l10n;
        $this->userId = $userId;
        $this->tagManager = $tagManager;
        $this->tagger = null;
        $this->db = $db;
        $this->DBController = $DBController;
    }

    /**
     * @NoAdminRequired
     * @param $trackid
     * @return JSONResponse
     */
    public function getAudioInfo($trackid)
    {

        $row = $this->DBController->getTrackInfo($trackid);
        $artist = $this->DBController->loadArtistsToAlbum($row['album_id'], $row['Album Artist']);
        $row['Album Artist'] = $artist;

        if ($row['Year'] === '0') $row['Year'] = $this->l10n->t('Unknown');
        if ($row['Bitrate'] !== '') $row['Bitrate'] = $row['Bitrate'] . ' kbps';

        array_splice($row, 15, 3);

        if ($row['Title']) {
            $result = [
                'status' => 'success',
                'data' => $row];
        } else {
            $result = [
                'status' => 'error',
                'data' => 'nodata'];
        }
        return new JSONResponse($result);
    }

    /**
     * @NoAdminRequired
     * @param $trackid
     * @return JSONResponse
     */
    public function getPlaylists($trackid)
    {
        $playlists = $this->DBController->getPlaylistsForTrack($this->userId, $trackid);
        if (!empty($playlists)) {
            $result = [
                'status' => 'success',
                'data' => $playlists];
        } else {
            $result = [
                'status' => 'error',
                'data' => 'nodata'];
        }
        return new JSONResponse($result);
    }

}
