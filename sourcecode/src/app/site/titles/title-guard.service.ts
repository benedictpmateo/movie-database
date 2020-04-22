import { Injectable } from '@angular/core';
import { HttpClient, HttpEvent, HttpParams, HttpRequest } from '@angular/common/http';
import { CanActivate, Router, ActivatedRouteSnapshot, RouterStateSnapshot, CanActivateChild, CanLoad, Route } from '@angular/router';
import { TitlesService, GetTitleResponse } from './titles.service';
import { Observable } from 'rxjs';
import { tap, map } from 'rxjs/operators';
import slugify from 'slugify';

@Injectable({
    providedIn: 'root',
})
export class TitleGuard implements CanActivate {
    constructor(
        private titles: TitlesService,
        private router: Router,
        private http: HttpClient
    ) {}

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean> | boolean {
        const titleId = route.params.titleId;

        return this.titles.get(titleId).pipe(map(response => {
            const item = response.title;
            const slug = slugify(item.name || '', { replacement: '-', lower: true, remove: /[*+~.()'"!:@]/g });

            if (item.is_series) {
                this.router.navigateByUrl(`/serial/${titleId}/${slug}`);
            } else {
                this.router.navigateByUrl(`/film/${titleId}/${slug}`);
            }
            return false;
        }));
    }
}
