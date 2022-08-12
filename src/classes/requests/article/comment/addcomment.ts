import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Messages } from "src/constants/messages";
import AddCommentInterface from "src/interfaces/requests/article/comment/addcomment.interface";

export default class AddComment{
    private _comment_text: string;
    private _http: HttpClient;
    private _permalink: string;
    private _token_key: string;
    private _url: string;

    constructor(data: AddCommentInterface){
        this._comment_text = data.comment_text;
        this._http = data.http;
        this._permalink = data.permalink;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get comment_text(){return this._comment_text;}
    get permalink(){return this._permalink;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async addComment(): Promise<object>{
        let response: object = {};
        try{
            const post_values: object = {
                'comment_text': this._comment_text,
                'permalink': this._permalink,
                'token_key': this._token_key
            };
            await this.addCommentPromise(post_values).then(res => {
                //console.log(res);
                response = JSON.parse(res);
                //console.log(response);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = {
                'done': false,
                'msg': Messages.COMMENTNEW_ERROR
            };
        }
        return response;
    }

    private async addCommentPromise(createData: object): Promise<string>{
        return await new Promise((resolve,reject)=>{
            const headers: HttpHeaders = new HttpHeaders({
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            });
            this._http.post(this.url,createData,{headers: headers, responseType: 'text'}).subscribe(res => {
              resolve(res);
            }, error => {
              reject(error);
            });
          });
    }
}