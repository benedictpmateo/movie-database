import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import {Settings} from '../../../core/config/settings.service';
import {Toast} from '../../../core/ui/toast.service';
import {finalize} from 'rxjs/operators';
import { HttpErrors } from '../../../core/http/errors/http-errors.enum';

@Component({
    selector: 'blueray',
    templateUrl: './blueray.component.html',
    styleUrls: ['./blueray.component.scss'],
    encapsulation: ViewEncapsulation.None
})
export class BluerayComponent implements OnInit {
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
        this.model['blueray.website'] = this.settings.get('blueray.website');
        this.model['blueray.referral'] = this.settings.get('blueray.referral');
        this.model['blueray.useTitle'] = this.settings.get('blueray.useTitle');
        this.model['blueray.disable'] = this.settings.get('blueray.disable');
        this.model['blueray.symbol'] = this.settings.get('blueray.symbol');
    }
}
