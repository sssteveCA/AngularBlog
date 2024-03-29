import { HttpClient, HttpErrorResponse, HttpHeaders } from "@angular/common/http";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
import DeleteArticleInterface from "src/interfaces/requests/article/deletearticle.interface";

export default class DeleteArticle{
    private _article_id: string;
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: DeleteArticleInterface){
        this._article_id = data.article_id;
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get article_id(){return this._article_id;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async deleteArticle(): Promise<object>{
        let response: object = {};
        try{
            let deletearticle_values: object = {
                article_id: this._article_id,
            };
            await this.deleteArticlePromise(deletearticle_values).then(res => {
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = { done: false };
            if(err instanceof HttpErrorResponse){
                let errorString: string = err.error as string;
                let errorBody: object = JSON.parse(errorString);
                response[Keys.MESSAGE] = errorBody[Keys.MESSAGE];
            }//if(err instanceof HttpErrorResponse){
            else{
                response[Keys.MESSAGE] = Messages.DELETEARTICLE_ERROR;
            }
        }
        return response;
    }

    private async deleteArticlePromise(deleteData: object): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders()
                .set('Content-Type','application/json')
                .set('Accept','application/json')
                .set(Keys.AUTH,this._token_key);
            this._http.post(this._url,deleteData,{headers: headers, responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
            })
        });
    }
}