<?php namespace Common\Core\Controllers;

use Common\Core\BootstrapData;
use Common\Core\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Common\Settings\Settings;
use App\ListModel;
use App\NewsArticle;
use App\Person;
use App\Title;
use App\Episode;
use Common\Pages\Page;
use Route;


class HomeController extends Controller {

    /**
     * @var BootstrapData
     */
    private $bootstrapData;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param BootstrapData $bootstrapData
     * @param Settings $settings
     */
    public function __construct(BootstrapData $bootstrapData, Settings $settings)
    {
        $this->bootstrapData = $bootstrapData;
        $this->settings = $settings;
    }

    private function find($array, $key, $value) {
        $item = 0;
        foreach($array as $struct) {
            if ($value == $struct[$key]) {
                $item = $struct;
                break;
            }
        }
        return $item;
    }

    private function defaultSeo()
    {
        $currentRoute = Route::getFacadeRoot()->current()->uri();
        $siteName = $this->settings->get('branding.site_name');
        $title = null;
        $description = null;
        $keywords = null;
        $currentUri =  basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $obj = array(
            'SITE_NAME' => $siteName,
        );



        switch ($currentRoute) {
            case '/':
                $tags = config('seo.home.show');
                $title = $this->settings->has('seo.home.show.og:title') ? 
                    $this->settings->get('seo.home.show.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.home.show.og:description') ? 
                    $this->settings->get('seo.home.show.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.home.show.keywords') ? 
                    $this->settings->get('seo.home.show.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case 'serial/{id}/{slug}/season/{season}/episode/{episode}':
                $cont1 = Title::find(request()->id);
                $cont2 = Episode::find(request()->episode);

                $obj = array(
                    'EPISODE.TITLE.NAME' => $cont1->name,
                    'EPISODE.TITLE.YEAR' => $cont1->year,
                    'EPISODE.DESCRIPTION' => $cont2->description,
                    'EPISODE.NAME' => $cont2->name,
                    'SITE_NAME' => $siteName,
                );

                $tags = config('seo.episode.show');
                $title = $this->settings->has('seo.episode.show.og:title') ? 
                    $this->settings->get('seo.episode.show.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.episode.show.og:description') ? 
                    $this->settings->get('seo.episode.show.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.episode.show.keywords') ? 
                    $this->settings->get('seo.episode.show.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case 'serial/{id}/{slug}/season/{season}':
                $cont = Title::find(request()->id);
                $obj = array(
                    'TITLE.NAME' => $cont->name,
                    'TITLE.DESCRIPTION' => !empty($cont->description) ? $cont->description : 'No overview has been added yet.',
                    'TITLE.YEAR' => $cont->year,
                    'TITLE.SEASON.NUMBER' => request()->season,
                    'SITE_NAME' => $siteName,
                );
                $tags = config('seo.season.show');
                $title = $this->settings->has('seo.season.show.og:title') ? 
                    $this->settings->get('seo.season.show.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.season.show.og:description') ? 
                    $this->settings->get('seo.season.show.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.season.show.keywords') ? 
                    $this->settings->get('seo.season.show.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case 'film/{id}/{slug}':
            case 'serial/{id}/{slug}':
                $cont = Title::find(request()->id);
                $obj = array(
                    'TITLE.NAME' => $cont->name,
                    'TITLE.DESCRIPTION' => !empty($cont->description) ? $cont->description : 'No overview has been added yet.',
                    'TITLE.YEAR' => $cont->year,
                    'SITE_NAME' => $siteName,
                );
                $tags = config('seo.title.show');
                $title = $this->settings->has('seo.title.show.og:title') ? 
                    $this->settings->get('seo.title.show.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.title.show.og:description') ? 
                    $this->settings->get('seo.title.show.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.title.show.keywords') ? 
                    $this->settings->get('seo.title.show.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case 'browse':
                $tags = config('seo.title.index');
                $title = $this->settings->has('seo.title.index.og:title') ? 
                    $this->settings->get('seo.title.index.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.title.index.og:description') ? 
                    $this->settings->get('seo.title.index.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.title.index.keywords') ? 
                    $this->settings->get('seo.title.index.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case 'lists/{id}':
                $cont = ListModel::find(request()->id);
                $obj = array(
                    'LIST.NAME' => $cont->name,
                    'LIST.DESCRIPTION' => $cont->description,
                    'SITE_NAME' => $siteName,
                );
                $tags = config('seo.list.show');
                $title = $this->settings->has('seo.list.show.og:title') ? 
                    $this->settings->get('seo.list.show.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.list.show.og:description') ? 
                    $this->settings->get('seo.list.show.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.list.show.keywords') ? 
                    $this->settings->get('seo.list.show.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case 'news':
                $tags = config('seo.news.index');
                $title = $this->settings->has('seo.news.index.og:title') ? 
                    $this->settings->get('seo.news.index.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.news.index.og:description') ? 
                    $this->settings->get('seo.news.index.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.news.index.keywords') ? 
                    $this->settings->get('seo.news.index.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case 'news/{id}':
                $cont = NewsArticle::find(request()->id);
                $obj = array(
                    'ARTICLE.TITLE' => $cont->title,
                    'ARTICLE.BODY' => $cont->body,
                    'ARTICLE.DESCRIPTION' => $cont->body,
                    'ARTICLE.DESCRIPTIONS' => $cont->body,
                    'SITE_NAME' => $siteName,
                );
                $tags = config('seo.news.show');
                $title = $this->settings->has('seo.news.show.og:title') ? 
                    $this->settings->get('seo.news.show.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.news.show.og:description') ? 
                    $this->settings->get('seo.news.show.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.news.show.keywords') ? 
                    $this->settings->get('seo.news.show.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case 'people':
                $tags = config('seo.person.index');
                $title = $this->settings->has('seo.person.index.og:title') ? 
                    $this->settings->get('seo.person.index.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.person.index.og:description') ? 
                    $this->settings->get('seo.person.index.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.person.index.keywords') ? 
                    $this->settings->get('seo.person.index.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case 'people/{id}':
                $cont = Person::find(request()->id);
                $obj = array(
                    'PERSON.NAME' => $cont->name,
                    'PERSON.DESCRIPTION' => $cont->description,
                    'SITE_NAME' => $siteName,
                );
                $tags = config('seo.person.show');
                $title = $this->settings->has('seo.person.show.og:title') ? 
                    $this->settings->get('seo.person.show.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.person.show.og:description') ? 
                    $this->settings->get('seo.person.show.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.person.show.keywords') ? 
                    $this->settings->get('seo.person.show.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case 'pages/{id}/{slug}':
                $cont = Page::where('slug', '=', request()->slug)->first();
                $obj = array(
                    'PAGE.TITLE' => $cont->title,
                    'PAGE.BODY' => $cont->body,
                    'SITE_NAME' => $siteName,
                );
                $tags = config('seo.page.show');
                $title = $this->settings->has('seo.page.show.og:title') ? 
                    $this->settings->get('seo.page.show.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                $description = $this->settings->has('seo.page.show.og:description') ? 
                    $this->settings->get('seo.page.show.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                $keywords = $this->settings->has('seo.page.show.keywords') ? 
                    $this->settings->get('seo.page.show.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                break;
            case '{all}':
                if (!empty(request()->input('query'))) {
                    $obj = array(
                        'QUERY' => request()->input('query'),
                        'SITE_NAME' => $siteName,
                    );
                    $tags = config('seo.search.index');
                    $title = $this->settings->has('seo.search.index.og:title') ? 
                        $this->settings->get('seo.search.index.og:title') : $this->find($tags, 'property', 'og:title')['content'];
                    $description = $this->settings->has('seo.search.index.og:description') ? 
                        $this->settings->get('seo.search.index.og:description') : $this->find($tags, 'property', 'og:description')['content'];
                    $keywords = $this->settings->has('seo.search.index.keywords') ? 
                        $this->settings->get('seo.search.index.keywords') : $this->find($tags, 'property', 'keywords')['content'];
                }
                break;
        }

        if (!empty($title)) {
            foreach ($obj as $key => $value) {
                $title = str_replace('{{'.$key.'}}', $value, $title);
                $description = str_replace('{{'.$key.'}}', $value, $description);
                $keywords = str_replace('{{'.$key.'}}', $value, $keywords);
            }
        }

        return array(
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
        );
    }

    /**
	 * @return View|Response
	 */
	public function show()
	{
        $htmlBaseUri = '/';

        //get uri for html "base" tag
        if (substr_count(url(''), '/') > 2) {
            $htmlBaseUri = parse_url(url(''))['path'] . '/';
        }

        if ($response = $this->handleSeo()) {
            return $response;
        }

        return response(view('app')
            ->with('bootstrapData', $this->bootstrapData->get())
            ->with('htmlBaseUri', $htmlBaseUri)
            ->with('settings', $this->settings)
            ->with('currentRoute', Route::getFacadeRoot()->current()->uri())
            ->with('seo', $this->defaultSeo())
        );
	}
}
