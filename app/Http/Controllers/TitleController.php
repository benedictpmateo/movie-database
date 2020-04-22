<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Image;
use App\Jobs\IncrementModelViews;
use App\Listable;
use App\Review;
use App\Season;
use App\Services\Titles\Retrieve\PaginateTitles;
use App\Services\Titles\Retrieve\ShowTitle;
use App\Services\Titles\Store\StoreTitleData;
use App\Title;
use App\Video;
use Common\Core\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Services\Data\Tmdb\TmdbApi;
use Illuminate\Support\Str;

class TitleController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Title
     */
    private $title;

    /**
     * @param Request $request
     * @param Title $title
     */
    public function __construct(Request $request, Title $title)
    {
        $this->request = $request;
        $this->title = $title;
    }

    public function xml () {
        $titles = $this->title
            ->whereHas('videos', function ($q) {
                $q->where('videos.source', '=', 'local')
                    ->where('videos.type', '=', 'embed');
            })
            ->with('videos', 'genres', 'credits')
            ->where('is_series', false)
            ->limit(1)
            ->get();

        $data = app(TmdbApi::class)->getTitle($titles[0]);
        

        return $this->success(['data' => $data]);
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        $this->authorize('index', Title::class);

        $pagination = app(PaginateTitles::class)->execute($this->request->all());

        return $this->success(['pagination' => $pagination]);
    }

    /**
     * @param string|integer $titleId
     * @param string $seasonNumber
     * @param string $episodeNumber
     * @return JsonResponse
     */
    public function show($titleId, $slug, $seasonNumber = null, $episodeNumber = null)
    {
        $this->authorize('show', Title::class);

        // season ID can be based in either via query or url params
        $params = $this->request->all();
        if ( ! isset($params['seasonNumber'])) $params['seasonNumber'] = $seasonNumber;
        if ( ! isset($params['episodeNumber'])) $params['episodeNumber'] = $episodeNumber;

        $response = app(ShowTitle::class)->execute($titleId, $params);

        $this->dispatch(new IncrementModelViews($response['title']));

        $type = 'title';
        $dataForSeo = null;
        if ($episodeNumber = Arr::get($params, 'episodeNumber')) {
            $type = 'episode';
            // need to specify data for seo generator manually as episode will be
            // nested under title and placeholder replacement will not work otherwise
            $episode = Arr::first($response['title']['season']['episodes'], function($episode) use($episodeNumber) {
                return $episode['episode_number'] === (int) $episodeNumber;
            });
            $dataForSeo = ['episode' => $episode];
        } else if (Arr::get($params, 'seasonNumber')) {
            $type = 'season';
        }

        $options = [
            'prerender' => [
                'view' => "$type.show",
                'config' => "$type.show",
                'dataForSeo' => $dataForSeo
            ]
        ];

        return $this->success($response, 200, $options);
    }

    public function redirect($titleId)
    {
        $this->authorize('show', Title::class);

        $title = $this->title->find($titleId);
        $slug = Str::slug($title->name, '-');

        if ($title->is_series) {
            return redirect('serial/'.$titleId.'/'.$slug);
        }
        return redirect('film/'.$titleId.'/'.$slug);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function update($id)
    {
        $this->authorize('update', Title::class);

        $data = $this->request->all();
        $title = $this->title->findOrFail($id);

        $title = app(StoreTitleData::class)->execute($title, $data);

        return $this->success(['title' => $title]);
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $this->authorize('store', Title::class);

        $title = $this->title->create($this->request->all());

        return $this->success(['title' => $title]);
    }

    public function destroy()
    {
        $this->authorize('destroy', Title::class);

        $titleIds = $this->request->get('ids');

        // seasons
        app(Season::class)->whereIn('title_id', $titleIds)->delete();

        // episodes
        $episodeIds = app(Episode::class)->whereIn('title_id', $titleIds)->pluck('id');
        app(Episode::class)->whereIn('id', $episodeIds)->delete();

        // images
        app(Image::class)
            ->whereIn('model_id', $titleIds)
            ->where('model_type', Title::class)
            ->delete();

        // list items
        app(Listable::class)
            ->whereIn('listable_id', $titleIds)
            ->where('listable_type', Title::class)
            ->delete();

        // reviews
        app(Review::class)
            ->whereIn('reviewable_id', $titleIds)
            ->where('reviewable_id', Title::class)
            ->delete();

        app(Review::class)
            ->whereIn('reviewable_id', $episodeIds)
            ->where('reviewable_id', Episode::class)
            ->delete();

        // tags
        DB::table('taggables')
            ->whereIn('taggable_id', $titleIds)
            ->where('taggable_type', Title::class)
            ->delete();

        // videos
        $videoIds = app(Video::class)
            ->whereIn('title_id', $titleIds)
            ->pluck('id');
        app(Video::class)->whereIn('id', $videoIds)->delete();

        DB::table('video_ratings')->whereIn('video_id', $videoIds)->delete();

        // titles
        $this->title->whereIn('id', $titleIds)->delete();

        return $this->success();
    }
}
