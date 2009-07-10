using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Collections;
using System.Data;

namespace GURUCORE.GForm.CodeGenerator
{
	public class MySqlSchemaReader : SchemaReader
	{
        public MySqlSchemaReader(IDbConnection p_oDbConn)
            : base(p_oDbConn)
		{
		}

		public override ArrayList GetSchemaObjects()
		{
			TypeMapper oTypeMapper = new TypeMapper("MySql5.xml");
			ArrayList arrResult = new ArrayList();
			string sSQL;
			//get all table
            sSQL =
"				select " +
" v1.TABLE_NAME,  v1.TABLE_TYPE,  v3.COLUMN_NAME " +
" from   INFORMATION_SCHEMA.TABLES v1 " +
" left join INFORMATION_SCHEMA.TABLE_CONSTRAINTS v2 " +
" on v1.TABLE_NAME = v2.TABLE_NAME " +
" and v2.CONSTRAINT_TYPE = 'PRIMARY KEY' " +
            
" left join INFORMATION_SCHEMA.COLUMNS v3 " +
" on  v1.TABLE_SCHEMA = v3.TABLE_SCHEMA " +
" and v1.TABLE_NAME = v3.TABLE_NAME ";

			m_oDbConn.Open();

			IDbCommand oDbCmdTbl = m_oDbConn.CreateCommand();
			oDbCmdTbl.CommandType = CommandType.Text;
			oDbCmdTbl.CommandText = sSQL;
			IDataReader oDrTbl = oDbCmdTbl.ExecuteReader();
			while (oDrTbl.Read())
			{
				string sName = oDrTbl["TABLE_NAME"].ToString();
				string sPrimaryKey = (oDrTbl["COLUMN_NAME"] == DBNull.Value) ? string.Empty : oDrTbl["COLUMN_NAME"].ToString();
				SchemaObject oSchemaObject = new SchemaObject(oDrTbl["TABLE_TYPE"].ToString(),sName,sPrimaryKey,oTypeMapper);

                if (sName.StartsWith(GlobalOptions.GetInstance().CurrentProfile.TablePrefixes))
				{
					arrResult.Add(oSchemaObject);
				}
			}
			oDrTbl.Close();

			foreach (SchemaObject oSchemaObject in arrResult)
			{
				string sName = oSchemaObject.Name;
				string sPrimaryKey = oSchemaObject.PrimaryKey;
				//select all column
				sSQL = 
					" select " +
					" 	COLUMN_NAME, " +
					" 	DATA_TYPE " +
					" from " +
					" 	INFORMATION_SCHEMA.COLUMNS " +
					" where " +
					" TABLE_NAME = '" + sName + "'";					

				IDbCommand oDbCmdCol = m_oDbConn.CreateCommand();
				oDbCmdCol.CommandType = CommandType.Text;
				oDbCmdCol.CommandText = sSQL;
				IDataReader oDrCol = oDbCmdCol.ExecuteReader();
				int nColCount = 0;
				while (oDrCol.Read())
				{
					string sColumnName = oDrCol["COLUMN_NAME"].ToString();
					string sDataType = oDrCol["DATA_TYPE"].ToString();
					SchemaObjectProperty oSchemaObjectProperty = new SchemaObjectProperty(sColumnName,sDataType,sColumnName == sPrimaryKey,sColumnName == sPrimaryKey,false);
					oSchemaObject.Properties.Add(oSchemaObjectProperty);
					nColCount ++;
				}
				oDrCol.Close();
			}

			m_oDbConn.Close();

			return arrResult;
		}
	}
}
