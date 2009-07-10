using System;
using System.Data;
using System.Collections;

namespace GURUCORE.GForm.CodeGenerator
{	
	public class SqlServerSchemaReader : SchemaReader
	{
		public SqlServerSchemaReader(IDbConnection p_oDbConn) : base(p_oDbConn)
		{
		}

		public override ArrayList GetSchemaObjects()
		{
			TypeMapper oTypeMapper = new TypeMapper("SQLServer2000.xml");
			ArrayList arrResult = new ArrayList();
			string sSQL;
			//get all table
			sSQL = 
				" select  " +
				" 	v1.TABLE_NAME, " +
				" 	v1.TABLE_TYPE, " +
				" 	v3.COLUMN_NAME " +
				" from  " +
				" 	INFORMATION_SCHEMA.TABLES v1 " +
				" 	left join INFORMATION_SCHEMA.TABLE_CONSTRAINTS v2 " +
				" 		on v1.TABLE_NAME = v2.TABLE_NAME and v2.CONSTRAINT_TYPE = 'PRIMARY KEY' " +
				" 	left join INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE v3 " +
				" 		on v2.CONSTRAINT_NAME = v3.CONSTRAINT_NAME " +
				" where " +
				" 	v1.TABLE_NAME NOT IN ('dtproperties','sysconstraints','syssegments') ";

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

                string sTablePrefixInConfig = GlobalOptions.GetInstance().CurrentProfile.TablePrefixes;
                if (string.IsNullOrEmpty(sTablePrefixInConfig) /*if Not config*/ ||
                    sName.StartsWith(sTablePrefixInConfig))
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
                sSQL = string.Format(
                    @" select COLUMN_NAME, DATA_TYPE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='{0}'",
                    sName);

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
