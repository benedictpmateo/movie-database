import {Injectable} from '@angular/core';
import {Title} from '../../models/title';
import {MEDIA_TYPE} from '../media-type';
import {Person} from '../../models/person';
import {Episode} from '../../models/episode';
import slugify from 'slugify';

@Injectable({
    providedIn: 'root'
})
export class TitleUrlsService {
    constructor() {}

    public mediaItem(item: Title|Person|Episode): any[] {
        const slug = slugify(item.name || '', { replacement: '-', lower: true, remove: /[*+~.()'"!:@]/g });
        if (item.type === MEDIA_TYPE.TITLE) {
            if (item.is_series) {
                return ['/serial', item.id, slug];
            }
            return ['/film', item.id, slug];
        } else if (item.type === MEDIA_TYPE.PERSON) {
            return ['/people', item.id];
        } else if (item.type === MEDIA_TYPE.EPISODE) {
            return ['/titles', item.title_id, 'season', item.season_number, 'episode', item.episode_number];
        }
    }

    public season(series: Title, seasonNumber: number) {
        return ['/titles', series.id, 'season', seasonNumber];
    }

    public episode(series: Title, season: number, episode: number) {
        return this.season(series, season).concat(['episode', episode]);
    }
}
