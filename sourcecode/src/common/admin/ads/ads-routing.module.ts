import {Route} from '@angular/router';
import {DownloadComponent} from './download/download.component';
import {DvdComponent} from './dvd/dvd.component';
import {BluerayComponent} from './blueray/blueray.component';
import {BannerComponent} from './banner/banner.component';
import {PopupsComponent} from './popups/popups.component';

export const vebtoAdsRoutes: Route[] = [
    {path: '', redirectTo: 'download', pathMatch: 'full'},
    {path: 'download', component: DownloadComponent, pathMatch: 'full'},
    {path: 'dvd', component: DvdComponent, pathMatch: 'full'},
    {path: 'blueray', component: BluerayComponent, pathMatch: 'full'},
    {path: 'banners', component: BannerComponent},
    {path: 'popups', component: PopupsComponent}
];
