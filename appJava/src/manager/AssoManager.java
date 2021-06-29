package manager;

import persistence.Asso;

import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

public class AssoManager extends Manager{


    public void insertAsso(Asso asso) throws SQLException {
        String query = "INSERT INTO association (numero_rna, password, name, email, signup_date, status, description ) VALUES (?, ?, ?, ?, ?, 0, ?); ";

        PreparedStatement stmt = db.prepareStatement(query);
        stmt.setString(1, asso.getNumeroRNA() );
        stmt.setString(2, asso.getPassword());
        stmt.setString(3, asso.getName());
        stmt.setString(4, asso.getEmail());
        stmt.setDate(5, asso.getSignupDate());
        stmt.setString(6, asso.getDescription());

        stmt.executeUpdate();
    }


    public Asso getAssoBySiren(String numeroRNA) throws SQLException {


        String query = "SELECT * FROM association WHERE numero_rna = ? ;";
        PreparedStatement stmt = db.prepareStatement(query);
        stmt.setString(1, numeroRNA);

        ResultSet rs = stmt.executeQuery();

        if (!rs.isBeforeFirst() ) {
            return new Asso();
        }
        else{
            rs.next();
            return new Asso(
                    rs.getInt("id"),
                    rs.getInt("status"),
                    rs.getString("numero_rna"),
                    rs.getString("password"),
                    rs.getString("name"),
                    rs.getString("email"),
                    rs.getString("description"),
                    rs.getDate("signup_date")
            );
        }
    }



}