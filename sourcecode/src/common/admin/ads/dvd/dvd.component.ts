import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import {Settings} from '../../../core/config/settings.service';
import {Toast} from '../../../core/ui/toast.service';
import {finalize} from 'rxjs/operators';
import { HttpErrors } from '../../../core/http/errors/http-errors.enum';

@Component({
    selector: 'dvd',
    templateUrl: './dvd.component.html',
    styleUrls: ['./dvd.component.scss'],
    encapsulation: ViewEncapsulation.None
})
export class DvdComponent implements OnInit {
    public model = {};
    public loading = false;

    constructor(
        public settings: Settings,
        private toast: Toast,
    ) {}

    ngOnInit() {
        this.hydrate();
    }

    public saveAds() {
        this.loading = true;
        this.settings.save({client: this.model})
            .pipe(finalize(() => this.loading = false))
            .subscribe(() => {
                this.toast.open('Ads have been updated.');
            }, () => {
                this.toast.open(HttpErrors.Default);
            });
    }

    public hydrate() {
        this.model['dvd.website'] = this.settings.get('dvd.website');
        this.model['dvd.referral'] = this.settings.get('dvd.referral');
        this.model['dvd.useTitle'] = this.settings.get('dvd.useTitle');
        this.model['dvd.disable'] = this.settings.get('dvd.disable');
        this.model['dvd.symbol'] = this.settings.get('dvd.symbol');
    }
}
