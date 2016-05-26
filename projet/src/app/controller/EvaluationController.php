<?php
namespace app\controller;
use app\model\Evaluation;
use app\model\Livre;
use app\model\Utilisateur;

class EvaluationController extends Controller {


    public function afficherEvaluation() {
        $evaluations = Evaluation::all();
        $this->app->view->setData('evaluations', $evaluations);
        $this->app->render('evaluation.php');
    }




    /**
     * affiche toutes les eval d'un livre identif par id
     * pour cette fct :
     * si je passe un param dans l'url : ?comment=true
     * alors j'affiche les evals qui ont un commentaire
     * si pas de param ou param à false, affichage de tous les comments meme les vides
     */
    public function afficherEvalsLivreIdJson($id){
        $comment = $this->app->request()->params('comment');
        $tabResult = array();
        $i = 0 ;
        // si le parametre comment est rentré
        if($comment == 'true'){
            $evals = Evaluation::where('idLivre', $id)->whereNotNull('commentaire')->get();
            foreach($evals as $e){
                $users = Utilisateur::find($e->idUtilisateur);
                $jsonEval = json_encode($e); // encode eval
                $jsonUser = json_encode($users); // encore user
                $tab = json_encode(array_merge(json_decode($jsonEval,true), json_decode($jsonUser,true)));
                $tabResult[$i] = $tab;
                $i++;
            }
            $tabResultJson = json_encode($tabResult);
            $this->app->response->headers->set('Content-Type', 'application/json');
            $this->app->response->body($tabResultJson);
        }else{
            $evals = Evaluation::where('idLivre', $id)->get();
            foreach($evals as $e){
                $users = Utilisateur::find($e->idUtilisateur);
                $jsonEval = json_encode($e); // encode eval
                $jsonUser = json_encode($users); // encore user
                $tab = json_encode(array_merge(json_decode($jsonEval,true), json_decode($jsonUser,true)));
                $tabResult[$i] = $tab;
                $i++;
            }
            $tabResultJson = json_encode($tabResult);
           // $b = json_encode($users);
           // $a = json_encode($evals);
            //$tab = json_encode(array_merge(json_decode($a,true), json_decode($b,true)));
            $this->app->response->headers->set('Content-Type', 'application/json');
            $this->app->response->body($tabResultJson);

        }

    }





    /**
     * ajoute une évaluation
     * verifie si l'utilisateur n'en à pas deja mise une
     */
    public function ajouterEvaluationJson($idUser, $idLivre){

        // recuperation des data sur la page
        $a = json_decode(file_get_contents('php://input'));

        date_default_timezone_set('Europe/Paris');
        $date = date('Y-m-d H:i:s');

        $evalSiExiste = Evaluation::where('idLivre', $idLivre)->where('idUtilisateur', $idUser)->count();
        if($evalSiExiste == 0){
            // il a pas rentré d'eval
            $eval = new Evaluation();
            $eval->idUtilisateur = $idUser;
            $eval->idLivre = $idLivre;
            $eval->note = $a->note;
            $eval->commentaire = $a->commentaire;
            if (isset($a->dateModification))
                $eval->dateModification = $a->dateModification;
            else
                $eval->dateModification = $date;
            $eval->save();
            $statut = 201;
            $b = json_encode($eval);
            $this->app->response->body($b);
        }else{
            // il a deja rentré une eval
            $statut = 403;
        }

        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->setStatus($statut);
    }





    /**
     * @param $idUser
     * @param $idLivre
     * modifie l'évaluation existente d'un user
     */
    public function modifierEvaluationJson($idUser, $idLivre, $idEval){
        $a = json_decode(file_get_contents('php://input'));

        date_default_timezone_set('Europe/Paris');
        if (isset($a->dateModification))
            $date = $a->dateModification;
        else
            $date = date('Y-m-d H:i:s');

        $evalSiExiste = Evaluation::where('idEvaluation', $idEval)->first();

        if($evalSiExiste->idUtilisateur == $idUser && $evalSiExiste->idLivre == $idLivre){
            // c'est ok pour la modif
            $evaluation = Evaluation::where('idLivre', $idLivre)->where('idUtilisateur', $idUser)->update([
                'note' => $a->note,
                'commentaire' => $a->commentaire,
                'dateModification' => $date
            ]);
            $statut = 204;
        }else{
            // c'est pas son eval
            $statut = 403;
        }

        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->setStatus($statut);
    }

    /**
     * Supprime une évaluation.
     * @param $idUser
     * @param $idLivre
     * @param $idEval
     */
    public function supprimerEvaluation($idUser, $idLivre, $idEval) {
        $evalSiExiste = Evaluation::find($idEval);

        if($evalSiExiste->idUtilisateur == $idUser && $evalSiExiste->idLivre == $idLivre){
            // c'est ok pour la suppression
            $evalSiExiste->destroy();
            $statut = 204;
        }else{
            // c'est pas son eval
            $statut = 403;
        }

        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->setStatus($statut);
    }

    //Ajax response example
    public function getEvaluation() {
        $evals = Evaluation::all();
        echo json_encode($evals);
    }

    public function create() {
        // MODIFIER POUR AVOIR LE BON CODE
        /*$id =  json_decode($_POST['id']);
        $name =  json_decode($_POST['name']);
        $usr = User();
        $usr->id = $id;
        $usr->name = $name;
        $usr->save();*/
    }


}








