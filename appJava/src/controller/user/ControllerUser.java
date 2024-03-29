package controller.user;


import controller.ControllerOne;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.text.Text;
import persistence.User;


public class ControllerUser extends ControllerOne {

    public User user;

    @FXML private Text availableCoinsField;
    @FXML private Text usedCoins;
    @FXML private Text userProjects;


    public void initData(User user){
        if(user != null) {
            this.user = user;
            availableCoinsField.setText(String.valueOf(user.getAvailableCoins()));
            usedCoins.setText(String.valueOf(user.getUsedCoins()));
            userProjects.setText(String.valueOf(user.getProjects()));
        }
        else
            goToLandingPage();
    }

    @FXML
    public void goBack(ActionEvent event){
        ControllerOne.loadUserIndex(event, user);
    }
}
