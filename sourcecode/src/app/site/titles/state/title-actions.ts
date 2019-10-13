import {GetTitleQueryParams, GetTitleResponse} from '../titles.service';
import {Review} from '../../../models/review';
import { Title } from 'app/models/title';

export class LoadTitle {
    static readonly type = '[Title] Load Title';
    constructor(public titleId: number, public params: GetTitleQueryParams = {}) {}
}

export class SetTitle {
    static readonly type = '[Title] Set Title';
    constructor(
        public response: GetTitleResponse,
        public params: GetTitleQueryParams = {},
    ) {}
}

export class LoadRelatedTitles {
    static readonly type = '[Title] Load Related Titles';
}

export class LoadReviews {
    static readonly type = '[Title] Load Reviews';
}

export class UpdateTitle {
    static readonly type = '[Title] Update Title';
    constructor(public title: Partial<Title>) {}
}

export class CrupdateReview {
    static readonly type = '[Title] Crupdate Review';
    constructor(public review: Review) {}
}

export class DeleteReview {
    static readonly type = '[Title] Delete Review';
    constructor(public review: Review) {}
}
